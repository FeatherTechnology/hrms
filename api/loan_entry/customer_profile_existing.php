<?php
require '../../ajaxconfig.php';

$aadhar_num = $_POST['aadhar_num'];
$result = array();
$qry = $pdo->query("SELECT * FROM `customer_profile` WHERE aadhar_num ='$aadhar_num' ORDER BY id DESC LIMIT 1");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}else{
    $result = 'New';
}
$pdo = null; //Close connection.

echo json_encode($result);