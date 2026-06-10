<?php

// to get the branch list based on the company we select
include '../../ajaxconfig.php';
$cmpy_id = $_POST['cmpy_id'];

$result = array();
$qry = $pdo->query("SELECT dc.department_name ,dc.id FROM company_department_mapping cdm LEFT JOIN department_creation dc on dc.id = cdm.department_id where cdm.company_id = $cmpy_id ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
