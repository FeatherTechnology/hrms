<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$designation_level = $_POST['designation_level'];

$result = array();

$qry = $pdo->query("
  SELECT 
    sc.id,
    sc.staff_name,
    di.designation,
    di.designation_level
FROM staff_creation sc

INNER JOIN occupation_info oi 
    ON oi.staff_profile_id = sc.id

INNER JOIN designation_creation di 
    ON di.id = oi.designation

WHERE oi.id = (
        SELECT MAX(oi2.id)
        FROM occupation_info oi2
        WHERE oi2.staff_profile_id = sc.id
    )
    
AND oi.company_id = '$company_id'
AND di.designation_level > $designation_level
AND sc.status = 1

ORDER BY di.designation_level ASC
");

if ($qry->rowCount() > 0) {

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

$pdo = null;
?>