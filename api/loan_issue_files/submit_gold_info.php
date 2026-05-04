<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$cus_id = $_POST['cus_id'];
$customer_profile_id = $_POST['customer_profile_id'];
$gold_type = $_POST['gold_type'];
$purity = $_POST['purity'];
$weight = $_POST['weight'];
$value = $_POST['value'];
$id = $_POST['id'];

$status = 0;
if ($id != '') {
    $qry = $pdo->query("UPDATE `gold_info` SET `cus_id`='$cus_id',`cus_profile_id`='$customer_profile_id',`gold_type`='$gold_type',`purity`='$purity',`weight`='$weight',`value`='$value',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id' ");
    $status = 1; //update
} else {
    $qry = $pdo->query("INSERT INTO `gold_info`(`cus_id`, `cus_profile_id`, `gold_type`, `purity`, `weight`, `value`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$customer_profile_id','$gold_type','$purity','$weight','$value','$user_id',now())");
    $status = 2; //Insert
}

echo json_encode($status);
