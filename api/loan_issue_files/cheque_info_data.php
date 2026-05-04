<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM `cheque_info` WHERE id='$id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$updresult = '';
$qry2 = $pdo->query("SELECT uploads FROM `cheque_upd` WHERE cheque_info_id='$id'");
if ($qry2->rowCount() > 0) {
    $updresult = $qry2->fetchAll(PDO::FETCH_ASSOC);
}

$noresult = '';
$qry3 = $pdo->query("SELECT cheque_no FROM `cheque_no_list` WHERE cheque_info_id='$id'");
if ($qry3->rowCount() > 0) {
    $noresult = $qry3->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close connection.

echo json_encode(array('result' => $result, 'upd' => $updresult, 'no' => $noresult));
