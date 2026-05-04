<?php
session_start();
include('../../../ajaxconfig.php');

$op_date = date('Y-m-d', strtotime($_POST['op_date']));

// Count all loan_issue entries for this date
$loanIssueQry = $pdo->query("SELECT COUNT(*) AS cnt FROM loan_issue WHERE DATE(created_on) = '$op_date'");
$loan_issue_count = $loanIssueQry->fetch()['cnt'];

// Count all accounts_loan_issued entries (hand cash + bank) for same date
$loanIssuedQry = $pdo->query("SELECT COUNT(*) AS cnt FROM accounts_loan_issued WHERE DATE(created_date) = '$op_date'");
$loan_issued_count = $loanIssuedQry->fetch()['cnt'];

// Compare
$response = ($loan_issue_count == $loan_issued_count) ? 0 : 1;

echo $response;

$pdo = null;
?>
