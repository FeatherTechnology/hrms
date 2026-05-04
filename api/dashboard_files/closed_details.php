<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$line_id = $_POST['lineId'];
$response = array();

//Total In Closed
$tot_le = "SELECT COALESCE(count(cp.id),0) AS total_closed FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 8  ";

//Total Consider
$tot_li = "SELECT COALESCE(count(cp.id),0) AS total_consider FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.sub_status = 1  ";

//Total Rejected
$tot_bal = "SELECT COALESCE(count(cp.id),0) AS total_rejected FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.sub_status = 2  ";

//Today In Closed
$today_le = "SELECT COALESCE(count(cp.id),0) AS today_closed FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 8 AND DATE(cs.updated_on) = CURDATE() ";

//Today Consider
$today_li = "SELECT COALESCE(count(cp.id),0) AS today_consider FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.sub_status = 1  AND DATE(cs.updated_on) = CURDATE() ";

//Today Rejected
$today_bal = "SELECT COALESCE(count(cp.id),0) AS today_rejected FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.sub_status = 2  AND DATE(cs.updated_on) = CURDATE() ";


if ($line_id != '') {
    $tot_le .= " AND cp.line IN($line_id) ";
    $tot_li .= " AND cp.line IN($line_id) ";
    $tot_bal .= " AND cp.line IN($line_id) ";
    $today_le .= " AND cp.line IN($line_id) ";
    $today_li .= " AND cp.line IN($line_id) ";
    $today_bal .= " AND cp.line IN($line_id) ";
} else {
    $tot_le .= " AND cp.insert_login_id = '$user_id'";
    $tot_li .= " AND cp.insert_login_id = '$user_id'";
    $tot_bal .= " AND cp.insert_login_id = '$user_id'";
    $today_le .= " AND cp.insert_login_id = '$user_id'";
    $today_li .= " AND cp.insert_login_id = '$user_id'";
    $today_bal .= " AND cp.insert_login_id = '$user_id'";
}

$qry = $pdo->query($tot_le);
$response['total_in_closed'] = $qry->fetch()['total_closed'];
$qry = $pdo->query($tot_li);
$response['total_consider'] = $qry->fetch()['total_consider'];
$qry = $pdo->query($tot_bal);
$response['total_rejected'] = $qry->fetch()['total_rejected'];
$qry = $pdo->query($today_le);
$response['today_in_closed'] = $qry->fetch()['today_closed'];
$qry = $pdo->query($today_li);
$response['today_consider'] = $qry->fetch()['today_consider'];
$qry = $pdo->query($today_bal);
$response['today_rejected'] = $qry->fetch()['today_rejected'];

echo json_encode($response);
