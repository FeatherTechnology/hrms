<?php
require '../../ajaxconfig.php';
@session_start();

$poll_titles_id = $_POST['poll_titles_id'];
$poll_value = $_POST['poll_value'];
$reason = $_POST['reason'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($poll_titles_id != '') {
    $qry = $pdo->query("INSERT INTO `poll_answers`(`poll_titles_id`, `poll_value`, `reason`, `insert_login_id`) VALUES ('$poll_titles_id', '$poll_value', '$reason', '$user_id')");

    if ($qry) {
        $result = 1; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
