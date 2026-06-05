<?php
// Get leave, permission, or week-off balance for the selected staff.

require "../../ajaxconfig.php";

$req_type = $_POST['req_type'];
$cmpy_id  = $_POST['cmpy_id'];
$staff_id = $_POST['staff_id'];

$query = "";
// to get the leave balance
if ($req_type == '1') {

    $query = " SELECT 
    lc.no_of_days,
    COALESCE(SUM(DATEDIFF(reg.to_date, reg.from_date) + 1), 0) AS used_count,
    (lc.no_of_days - COALESCE(SUM(DATEDIFF(reg.to_date, reg.from_date) + 1), 0)) AS balance

FROM leave_creation lc

LEFT JOIN regularization reg  
    ON reg.company_id = lc.company_id  
    AND reg.req_type = :req_type 
    AND reg.staff_profile_id = :staff_id 
    AND YEAR(reg.from_date) = YEAR(CURDATE())
    AND YEAR(reg.to_date) = YEAR(CURDATE())
    AND reg.leave_type = :cmpy_id
    AND reg.status = 1

WHERE lc.id = :cmpy_id; ";

    // to get the permission balance
} else if ($req_type == '2') {

    $query = "  SELECT 
    cp.max_permission,
    COUNT(reg.id) AS used_count,
    (cp.max_permission - COUNT(reg.id)) AS balance

FROM company_policies cp
LEFT JOIN regularization reg
    ON reg.company_id = cp.company_id
    AND reg.req_type = :req_type
    AND reg.staff_profile_id = :staff_id
    AND YEAR(reg.from_date) = YEAR(CURDATE())
    AND YEAR(reg.to_date) = YEAR(CURDATE())
    AND reg.status = 1
WHERE cp.company_id = :cmpy_id
GROUP BY cp.max_permission ";

    // to get the week off balance
} else {
    $query = " SELECT 
    SUM(cw.week_off),
    
    COALESCE(
        SUM(DATEDIFF(reg.to_date, reg.from_date) + 1), 0
    ) AS used_count,

    (
        SUM(cw.week_off) - 
        COALESCE(SUM(DATEDIFF(reg.to_date, reg.from_date) + 1), 0)
    ) AS balance

FROM company_weekoffs cw

LEFT JOIN company_policies cp 
    ON cp.id = cw.company_policies_id

LEFT JOIN regularization reg
    ON reg.company_id = cp.company_id
    AND reg.req_type = :req_type
    AND reg.staff_profile_id = :staff_id
    AND MONTH(reg.from_date) = MONTH(CURDATE())
    AND MONTH(reg.to_date) = MONTH(CURDATE())
    AND reg.status = 1

WHERE cp.company_id = :cmpy_id; ";
}

$stmt = $pdo->prepare($query);

$stmt->bindParam(':req_type', $req_type, PDO::PARAM_INT);
$stmt->bindParam(':cmpy_id', $cmpy_id, PDO::PARAM_INT);
$stmt->bindParam(':staff_id', $staff_id);

$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($result);
