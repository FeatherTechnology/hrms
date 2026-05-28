<?php
require '../../ajaxconfig.php';
@session_start();

$designation = $_POST['designation'];
$designation_level = $_POST['designation_level'];
$designation_id = $_POST['designation_id']; 
$user_id = $_SESSION['user_id'];

$result = 0;

if ($designation_id != '') {
    $qry = $pdo->query("UPDATE `designation_creation` SET `designation`='$designation', `designation_level`='$designation_level', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$designation_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `designation_creation`(`designation`, `designation_level`, `insert_login_id`, `created_date`) VALUES ('$designation', '$designation_level', '$user_id', now())");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
