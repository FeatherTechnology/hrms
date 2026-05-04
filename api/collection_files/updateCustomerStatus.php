<?php
require '../../ajaxconfig.php';

$userid = $_POST['userid'];
$cp_id = $_POST['cp_id'];
$sub_sts = $_POST['follow_cus_sts'];
$curdate = date('Y-m-d');
$query = $pdo->query("UPDATE `customer_status` SET `coll_status`='$sub_sts', `insert_login_id`='$userid', `created_on`=NOW() WHERE `cus_profile_id`='$cp_id' ");

echo json_encode($query ? 1 : 2);
