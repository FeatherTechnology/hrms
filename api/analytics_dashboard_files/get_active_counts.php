<?php
// Get active Feedback, Rating, and Poll counts where status is active and the end date has not expired.

include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'];

$type = $_POST['type'] ?? '';

// GET COMPANY ID
$getUser = $pdo->query(" SELECT company_id FROM users  WHERE id = '$user_id'");

$userData = $getUser->fetch(PDO::FETCH_ASSOC);
$company_id = $userData['company_id'];

$currentDateTime = date('Y-m-d H:i:s');

// SET TABLE & STATUS COLUMN
$table = '';
$status_column = '';

if ($type == 'feedback') {
    $table = 'feedback_titles';
    $status_column = 'feedback_status';
} elseif ($type == 'poll') {
    $table = 'poll_titles';
    $status_column = 'poll_status';
} elseif ($type == 'rating') {
    $table = 'rating_titles';
    $status_column = 'rating_status';
} else {
    echo 0;
    exit;
}

// GET COUNT
$stmt = $pdo->query("SELECT COUNT(*) as total
    FROM $table
    WHERE company_id = '$company_id'
    AND $status_column = 0
    AND '$currentDateTime' <= end_date_time
");

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['total'];

$pdo = null;
