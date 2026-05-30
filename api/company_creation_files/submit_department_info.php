<?php
require '../../ajaxconfig.php';
@session_start();

$department_code = $_POST['department_code']; 
$department_name = $_POST['department_name'];
$department_id = $_POST['department_id']; 
$user_id = $_SESSION['user_id'];

$result = 0;

if ($department_id != '') {
    $qry = $pdo->query("UPDATE `department_creation` SET `department_code`='$department_code', `department_name`='$department_name', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$department_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `department_creation`(`department_code`, `department_name`, `insert_login_id`, `created_date`) VALUES ('$department_code', '$department_name', '$user_id', now())");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
