<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$fine_cp_id = $_POST['fine_cp_id'];
$cus_id = $_POST['cus_id'];
$fine_date = date('Y-m-d', strtotime($_POST['fine_date']));
$fine_purpose = $_POST['fine_purpose'];
$fine_Amnt = $_POST['fine_Amnt'];

$qry = $pdo->query("INSERT INTO `collection_charges`( `cus_profile_id`, `cus_id`, `coll_date`, `coll_purpose`, `coll_charge`, `status`, `insert_login_id`, `created_date`) VALUES ('$fine_cp_id','$cus_id','$fine_date','$fine_purpose','$fine_Amnt','0','$user_id', now()) ");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

echo json_encode($result);
