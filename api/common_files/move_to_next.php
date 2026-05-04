<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cus_sts_tableid = $_POST['cus_sts_id'];
$cus_sts = $_POST['cus_sts'];
$result = array();
$qry = $pdo->query("UPDATE `customer_status` SET `status`='$cus_sts', `update_login_id`='$user_id', `updated_on`=now() WHERE `id`='$cus_sts_tableid' ");
if($qry){
    $result = 0;
}
$pdo=null; //Close Connection.
echo json_encode($result);
?>