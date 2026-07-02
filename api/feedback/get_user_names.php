<?php

// Fetch all active users who have Feedback Access enabled.

include '../../ajaxconfig.php';

$result = [];

$qry = $pdo->prepare("SELECT id , user_name FROM users WHERE feedback_access = '1' AND status = 0");
$qry->execute();

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null;

echo json_encode($result);
