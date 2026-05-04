<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$line_id = $_POST['lineId'];
$response = array();

//Total Approval
$tot_le = "SELECT COALESCE(count(cp.id),0) AS total_approval FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 4  AND cs.status != 5 AND cs.status != 6 ";

//Total Loan Issued
$tot_li = "SELECT COALESCE(count(cp.id),0) AS total_issued FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 7  ";

//Total Balance
$tot_bal = "SELECT COALESCE(count(cp.id),0) AS total_balance FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status > 3 AND cs.status < 7 AND cs.status != 5 AND cs.status != 6 ";

//Today Approval
$today_le = "SELECT COALESCE(count(cp.id),0) AS today_approval FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 4  AND cs.status != 5 AND cs.status != 6 AND DATE(cs.updated_on) = CURDATE() ";

//Today Loan Issued
$today_li = "SELECT COALESCE(count(cp.id),0) AS today_issued FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status >= 7  AND DATE(cs.updated_on) = CURDATE() ";

//Today Balance
$today_bal = "SELECT COALESCE(count(cp.id),0) AS today_balance FROM `customer_profile` cp JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id JOIN customer_status cs ON cp.id = cs.cus_profile_id WHERE cs.status > 3 AND cs.status < 7 AND cs.status != 5 AND cs.status != 6  AND DATE(cs.updated_on) = CURDATE() ";


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
$response['total_approved'] = $qry->fetch()['total_approval'];
$qry = $pdo->query($tot_li);
$response['total_loan_issued'] = $qry->fetch()['total_issued'];
$qry = $pdo->query($tot_bal);
$response['total_approve_balance'] = $qry->fetch()['total_balance'];
$qry = $pdo->query($today_le);
$response['today_approved'] = $qry->fetch()['today_approval'];
$qry = $pdo->query($today_li);
$response['today_loan_issued'] = $qry->fetch()['today_issued'];
$qry = $pdo->query($today_bal);
$response['today_approve_balance'] = $qry->fetch()['today_balance'];

echo json_encode($response);
