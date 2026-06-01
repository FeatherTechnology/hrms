<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$selected_dept = $_POST['selected_dept'];

$result = array();

$qry = $pdo->query("
    SELECT DISTINCT di.id, di.department_name
    FROM department_creation di

    LEFT JOIN company_department_mapping cd ON di.id = cd.department_id
    WHERE di.department_status = 0
    AND (
        cd.company_id = '$company_id'
        OR di.id = '$selected_dept'
    )
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);
?>