<?php
require "../../../ajaxconfig.php";
session_start();
$user_id = $_SESSION['user_id'];

$credit = '';
$debit = '';

$bank_id = $_POST['bank_id'];
$acc_no = $_POST['acc_no'];
$trans_date = $_POST['transaction_date'];
$trans_id = $_POST['transaction_id'];
$narration = $_POST['narration'];
$crdb = $_POST['cr_dr'];
$amt = $_POST['amount'];
if ($crdb == 1) {
    $credit = $amt;
} else if ($crdb == 2) {
    $debit = $amt;
}
$balance = $_POST['balance'];

$qry = $pdo->query("INSERT INTO `bank_clearance`(`bank_id`, `trans_date`, `narration`,`trans_id`, `credit`, `debit`, `balance`, `insert_login_id`, `created_date`) 
VALUES ('$bank_id','$trans_date','$narration','$trans_id','$credit','$debit','$balance','$user_id',now() )");

if ($qry) {
    $response = 1;
} else {
    $response = 2;
}

echo json_encode($response);
