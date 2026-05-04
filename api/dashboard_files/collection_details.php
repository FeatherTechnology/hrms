<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$line_id = $_POST['lineId'];
$response = array();

//Total Paid
$tot_paid = "SELECT COALESCE(SUM(total_paid_track),0) AS total_paid FROM `collection` c WHERE 1  ";

//Total Penalty
$tot_penalty = "SELECT COALESCE(SUM(c.penalty_track),0) AS total_penalty FROM `collection` c WHERE 1  ";

//Total Fine
$tot_fine = "SELECT COALESCE(SUM(c.coll_charge_track),0) AS total_fine FROM `collection` c WHERE 1  ";

//Today Paid
$today_paid = "SELECT COALESCE(SUM(total_paid_track),0) AS today_paid FROM `collection` c WHERE DATE(created_date) = CURDATE() ";

//Today Penalty
$today_penalty = "SELECT COALESCE(SUM(c.penalty_track),0) AS today_penalty FROM `collection` c WHERE DATE(c.created_date) = CURDATE() ";

//Today Fine
$today_fine = "SELECT COALESCE(SUM(c.coll_charge_track),0) AS today_fine FROM `collection` c WHERE DATE(c.created_date) = CURDATE() ";


if ($line_id != '') {
    $tot_paid .= " AND c.line IN($line_id) ";
    $tot_penalty .= " AND c.line IN($line_id) ";
    $tot_fine .= " AND c.line IN($line_id) ";
    $today_paid .= " AND c.line IN($line_id) ";
    $today_penalty .= " AND c.line IN($line_id) ";
    $today_fine .= " AND c.line IN($line_id) ";
} else {
    $tot_paid .= " AND c.insert_login_id = '$user_id'";
    $tot_penalty .= " AND c.insert_login_id = '$user_id'";
    $tot_fine .= " AND c.insert_login_id = '$user_id'";
    $today_paid .= " AND c.insert_login_id = '$user_id'";
    $today_penalty .= " AND c.insert_login_id = '$user_id'";
    $today_fine .= " AND c.insert_login_id = '$user_id'";
}

$qry = $pdo->query($tot_paid);
$response['total_paid'] = $qry->fetch()['total_paid'];
$qry = $pdo->query($tot_penalty);
$response['total_penalty'] = $qry->fetch()['total_penalty'];
$qry = $pdo->query($tot_fine);
$response['total_fine'] = $qry->fetch()['total_fine'];
$qry = $pdo->query($today_paid);
$response['today_paid'] = $qry->fetch()['today_paid'];
$qry = $pdo->query($today_penalty);
$response['today_penalty'] = $qry->fetch()['today_penalty'];
$qry = $pdo->query($today_fine);
$response['today_fine'] = $qry->fetch()['today_fine'];

echo json_encode($response);
