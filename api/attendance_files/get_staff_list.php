<?php

// to get the staff list based on the company department branch 
include '../../ajaxconfig.php';

$company_id = $_POST['cmpy_id'];
$dept_id    = $_POST['dep_name'];
$branch_name     = $_POST['branch_name'];
if (empty($company_id) || empty($dept_id) || empty($branch_name)) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT 
        sc.id,
        sc.staff_name
    FROM staff_creation sc

    INNER JOIN (
        SELECT *
        FROM occupation_info
        WHERE id IN (
            SELECT MAX(id)
            FROM occupation_info
            GROUP BY staff_profile_id
        )
    ) oi ON sc.id = oi.staff_profile_id

    WHERE  oi.company_id = $company_id  AND oi.branch_id = $branch_name AND  oi.department = $dept_id 

    ORDER BY sc.staff_name ASC
";

$qry = $pdo->query($sql);

$result = array();

if ($qry->rowCount() > 0) {

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);
