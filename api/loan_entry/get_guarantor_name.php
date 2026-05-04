<?php
//Also using in property holder name, KYC Family Member
//Aslo Using in Loan Issue.
//Also using in NOC.
require '../../ajaxconfig.php';

$response =array();
$cus_id = $_POST['cus_id'];
$qry = $pdo->query("SELECT id, fam_name FROM  family_info WHERE cus_id = '$cus_id' ");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);