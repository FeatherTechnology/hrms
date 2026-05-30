<?php
require '../../ajaxconfig.php';
@session_start();

$company_id = $_POST['company_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$no_of_days = $_POST['no_of_days'];
$holiday_name = $_POST['holiday_name'];
$user_id = $_SESSION['user_id'];
$holiday_id = $_POST['holiday_id'];

$result = 0;

if ($holiday_id != '') {
    $qry = $pdo->query("UPDATE `holiday_creation` SET `company_id`='$company_id', `from_date`='$from_date', `to_date`='$to_date', `no_of_days`='$no_of_days', `holiday_name`='$holiday_name', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$holiday_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `holiday_creation` (`company_id`, `from_date`, `to_date`, `no_of_days`, `holiday_name`, `insert_login_id`) VALUES ('$company_id', '$from_date', '$to_date', '$no_of_days', '$holiday_name', '$user_id')");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
