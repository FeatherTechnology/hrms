<?php
session_start();
include '../../ajaxconfig.php';

$user_name = $_POST['user_name'];
$password = $_POST['password'];

$qry = $pdo->prepare("SELECT `id` FROM users WHERE `user_name` = ? AND `password` = ?");
$qry->execute([$user_name, $password]);
$row = $qry->fetch();
$count = $qry->rowCount();
if($count > 0){
    $_SESSION['user_id'] = $row['id'];
    $response = 'Success';
}else{
    $response = 'Error';
}
echo json_encode($response);
