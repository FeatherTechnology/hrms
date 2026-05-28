<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$shift_name = $_POST['shift_name'];
$start_time = date("H:i:s", strtotime($_POST['start_time']));
$end_time = date("H:i:s", strtotime($_POST['end_time']));
$shift_time = $_POST['shift_time'];
$grace_time = $_POST['grace_time'];
$shift_id = $_POST['shift_id'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($shift_id != '') {
    $qry = $pdo->query("UPDATE `shift_creation` SET `company_id`='$company_name', `shift_name`='$shift_name', `start_time`='$start_time', `end_time`='$end_time', `shift_time`='$shift_time', `grace_time`='$grace_time', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$shift_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `shift_creation` (`company_id`, `shift_name`, `start_time`, `end_time`, `shift_time`, `grace_time`, `insert_login_id`, `created_date`) VALUES ('$company_name', '$shift_name', '$start_time', '$end_time', '$shift_time', '$grace_time', '$user_id', now())");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
