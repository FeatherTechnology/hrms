<?php
require '../../ajaxconfig.php';
$cp_id = $_POST['cp_id'];
$response = array();
$qry = $pdo->query("SELECT id, cheque_no FROM cheque_no_list WHERE cus_profile_id = '$cp_id' AND used_status = 0 ");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);
