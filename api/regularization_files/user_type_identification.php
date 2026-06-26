<?php
include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT allowed_request_type, approval_required , user_type
    FROM users
    WHERE id = ?
");
$stmt->execute([$userid]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($user);
