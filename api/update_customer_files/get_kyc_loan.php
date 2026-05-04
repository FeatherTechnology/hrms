<?php
//Also using in property holder name, KYC Family Member
//Aslo Using in Loan Issue.
require '../../ajaxconfig.php';

$response =array();
$cus_id = $_POST['cus_id'];
$qry = $pdo->query("SELECT cus_profile_id, loan_id FROM  loan_entry_loan_calculation WHERE cus_id = '$cus_id' ");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);