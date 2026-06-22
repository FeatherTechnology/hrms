<?php
session_start();
include '../../ajaxconfig.php';

$user_name = $_POST['user_name'];
$password = $_POST['password'];
$status = 0;

$qry = $pdo->prepare("SELECT `id` FROM users WHERE `user_name` = ? AND `password` = ? AND `status` = ? AND (relieve_date IS NULL OR relieve_date >= CURDATE())");
$qry->execute([$user_name, $password, $status]);
$row = $qry->fetch();
$count = $qry->rowCount();

if ($count > 0) {
    $_SESSION['user_id'] = $row['id'];
    $response = 'Success';
} else {
    $response = 'Error';
}

echo json_encode($response);
