<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$leave_type = $_POST['leave_type'];
$no_of_days = $_POST['no_of_days'];
$leave_criteria_id = $_POST['leave_criteria_id'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($leave_criteria_id != '') {
    $qry = $pdo->query("UPDATE `leave_creation` SET `company_id`='$company_name', `leave_type`='$leave_type', `no_of_days`='$no_of_days', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$leave_criteria_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `leave_creation`(`company_id`, `leave_type`, `no_of_days`, `insert_login_id`, `created_date`) VALUES ('$company_name', '$leave_type', '$no_of_days', '$user_id', now())");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
