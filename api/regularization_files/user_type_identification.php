<?php
include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
$stmt->execute([$userid]);

$user_type = $stmt->fetchColumn();

echo json_encode([
    'user_type' => $user_type
]);
