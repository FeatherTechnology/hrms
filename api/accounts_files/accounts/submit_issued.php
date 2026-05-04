<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$userid = $_POST['id'];
$line = $_POST['line'];
$no_of_bills = $_POST['no_of_bills'];
$netcash = str_replace(',', '', $_POST['netcash']);
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];
$op_date = date('Y-m-d H:i:s', strtotime($_POST['op_date'] . ' ' . date('H:i:s')));

$qry = $pdo->query("INSERT INTO `accounts_loan_issued`( `user_id`, `line`, `coll_mode`, `no_of_bills`, `bank_id`, `netcash`, `insert_login_id`, `created_date`) VALUES ('$userid','$line','$cash_type','$no_of_bills','$bank_id','$netcash','$user_id','$op_date')");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
