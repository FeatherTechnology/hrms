<?php
require '../../ajaxconfig.php';
@session_start();

$general_feedback_id = $_POST['general_feedback_id'];
$company_id = $_POST['company_id'];
$feedback_name = $_POST['feedback_name'];
$status = $_POST['status'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($general_feedback_id != '') {
    $qry = $pdo->query("UPDATE `general_feedback` SET `company_id`='$company_id', `feedback_name`='$feedback_name', `status`='$status', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$general_feedback_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `general_feedback` (`company_id`, `feedback_name`, `status`, `insert_login_id`) VALUES ('$company_id', '$feedback_name', '$status', '$user_id')");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
