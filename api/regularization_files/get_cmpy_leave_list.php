<?php
include '../../ajaxconfig.php';
if (isset($_POST['cmpy_id']) && $_POST['cmpy_id'] != '') {
    $cmpy_id = $_POST['cmpy_id'];
}

$result = array();
$qry = $pdo->query("SELECT leave_type, id ,no_of_days FROM company_leave_policies where status = 0 and company_id = $cmpy_id ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
