<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$selected_designation = $_POST['selected_designation'];

$result = array();

$qry = $pdo->query("
    SELECT DISTINCT 
        des.id,
        des.designation,
        des.designation_level
    FROM designation_info des
    LEFT JOIN company_designation_mapping cd 
        ON des.id = cd.designation_id
    WHERE des.designation_status = 0
    AND (
        cd.company_id = '$company_id'
        OR des.id = '$selected_designation'
    )
    ORDER BY des.designation_level ASC
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

$pdo = null;
?>