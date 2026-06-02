<?php
require '../../ajaxconfig.php';
@session_start();

$rating_titles_id = $_POST['rating_titles_id'];
$rating_value = $_POST['rating_value'];
$reason = $_POST['reason'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($rating_titles_id != '') {
    $qry = $pdo->query("INSERT INTO `rating_answers`(`rating_titles_id`, `rating_value`, `reason`, `insert_login_id`) VALUES ('$rating_titles_id', '$rating_value', '$reason', '$user_id')");

    if ($qry) {
        $result = 1; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
