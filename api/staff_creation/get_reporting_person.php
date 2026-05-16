<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$designation_level = $_POST['designation_level'];

$result = array();

/*
Need Reporting Person = Staff Name
whose designation level is greater than selected level
*/
$qry = $pdo->query("
    SELECT 
        sc.id,
        sc.staff_name,
        di.designation,
        di.designation_level
    FROM staff_creation sc

    JOIN occupation_info oi 
        ON sc.id = oi.staff_profile_id

    JOIN designation_info di 
        ON oi.designation = di.id

    WHERE oi.company_id = '$company_id'
    AND di.designation_level > '$designation_level'
    AND sc.status = '1'

    ORDER BY di.designation_level ASC
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

$pdo = null;
?>