<?php
require '../../ajaxconfig.php';
@session_start();

$notice_per_served = $_POST['notice_per_served'];
$exit_type = $_POST['exit_type'];
$reason = $_POST['reason'];
$last_wrk_day = $_POST['last_wrk_day'];
$user_id = $_SESSION['user_id'];
$staff_profile_id = $_POST['staff_profile_id'];

$qry = $pdo->query("UPDATE `staff_creation` SET `notice_per_served`='$notice_per_served',`relieve_date`='$last_wrk_day',`exit_type`='$exit_type',`reason`='$reason',`status`= 2,`update_login_id`='$user_id',updated_on = now() WHERE `id`='$staff_profile_id'");
if ($qry) {
    $result = 1; //update
} else {
    $result = 0; //update
}


echo json_encode($result);
