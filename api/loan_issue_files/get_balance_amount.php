<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cus_id = $_POST['cus_id'];
$payment_mode = $_POST['payment_mode'];

if ($payment_mode == '1') {
    $cndtn = "coll_mode = '1'";
    $cndtn1 = "payment_mode = '1'";
    $alert_message = 'Insufficient Amount in Your Hand';
} else {
    $cndtn = "coll_mode != '1'";
    $cndtn1 = "payment_mode IN ('2', '3')";
    $alert_message = 'Insufficient Amount in Your Bank';
}

$qry = $pdo->query("SELECT COALESCE(SUM(a.amount), 0) AS amount,COALESCE(oli.overallIssueAmnt, 0) AS overallIssueAmnt FROM other_transaction a
    LEFT JOIN (SELECT insert_login_id, SUM(issue_amnt) AS overallIssueAmnt FROM loan_issue WHERE $cndtn1 GROUP BY insert_login_id
    ) oli ON oli.insert_login_id = a.user_name WHERE a.user_name = '$user_id' AND a.trans_cat = '7' AND $cndtn ");
if ($qry->rowCount() > 0) {
    while ($data = $qry->fetch(PDO::FETCH_ASSOC)) {
        $amnt = ($data['amount']) ? $data['amount'] : 0;
        $overallIssueAmnt = ($data['overallIssueAmnt']) ? $data['overallIssueAmnt'] : 0;
        $balance = $amnt - $overallIssueAmnt;
    }
}

echo json_encode(['balance' => $balance, 'alert_message' => $alert_message]);