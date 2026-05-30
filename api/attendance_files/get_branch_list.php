<?php
include '../../ajaxconfig.php';
$cmpy_id = $_POST['cmpy_id'];

$result = array();
$qry = $pdo->query("SELECT branch_name ,id FROM branch_creation where company_id = $cmpy_id ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
