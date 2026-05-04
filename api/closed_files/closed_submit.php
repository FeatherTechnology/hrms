<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$cus_profile_id = $_POST['cus_profile_id'];
$sub_status = $_POST['sub_status'];
$remark = $_POST['remark'];

$qry = $pdo->query("UPDATE `customer_status` SET `status`='9',`sub_status`='$sub_status',`closed_date`=now(),`remark`='$remark',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cus_profile_id' ");
if ($qry) {
    $result = 1; //success
} else {
    $result = 2; //failed

}

echo json_encode($result);
