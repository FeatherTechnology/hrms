<?php
require '../../ajaxconfig.php';
@session_start();

$team_code = $_POST['team_code'];
$team_name = $_POST['team_name'];
$team_id = $_POST['team_id'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($team_id != '') {
    $qry = $pdo->query("UPDATE `team_name_creation` SET `team_code`='$team_code', `team_name`='$team_name', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$team_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `team_name_creation`(`team_code`, `team_name`, `insert_login_id`, `created_date`) VALUES ('$team_code', '$team_name', '$user_id', now())");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
