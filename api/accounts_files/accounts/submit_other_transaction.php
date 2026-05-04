<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$coll_mode = $_POST['coll_mode'];
$bank_id = $_POST['bank_id'];
$trans_category = $_POST['trans_category'];
$other_trans_name = $_POST['other_trans_name'];
$cat_type = $_POST['cat_type'];
$other_ref_id = $_POST['other_ref_id'];
$other_trans_id = $_POST['other_trans_id'];
// $other_user_name = $_POST['other_user_name'];
$other_amnt = $_POST['other_amnt'];
$other_remark = $_POST['other_remark'];
$op_date = date('Y-m-d H:i:s', strtotime($_POST['op_date'] . ' ' . date('H:i:s')));

$qry = $pdo->query("INSERT INTO `other_transaction`( `coll_mode`, `bank_id`, `trans_cat`, `name`, `type`, `ref_id`, `trans_id`, `amount`, `remark`, `insert_login_id`, `created_on`) VALUES ('$coll_mode','$bank_id','$trans_category','$other_trans_name','$cat_type','$other_ref_id','$other_trans_id','$other_amnt','$other_remark','$user_id', '$op_date')");

if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
