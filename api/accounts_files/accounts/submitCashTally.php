<?php
session_start();
$user_id = $_SESSION['user_id'];

include('../../../ajaxconfig.php');

$op_date = date('Y-m-d', strtotime($_POST['op_date']));
$opening_bal = $_POST['opening_bal'];
$open_hand_cash = $_POST['open_hand_cash'];
$open_bank_cash = $_POST['open_bank_cash'];
$close_date = date('Y-m-d', strtotime($_POST['op_date']));
$closing_bal = $_POST['closing_bal'];
$close_hand_cash = $_POST['close_hand_cash'];
$close_bank_cash = $_POST['close_bank_cash'];
$untracked_total = $_POST['untracked_total'];

$qry = $pdo->query("INSERT INTO `cash_tally`
(`open_hand_cash`, `open_date`, `open_bank_cash`, `opening_bal`, `close_date`, `close_hand_cash`, `close_bank_cash`, `bank_untrkd`, `closing_bal`,
`insert_login_id`, `created_date`)
VALUES 
('$open_hand_cash', '$op_date', '$open_bank_cash', '$opening_bal', '$close_date', '$close_hand_cash', '$close_bank_cash', '$untracked_total', '$closing_bal',
'$user_id', NOW() )");

if ($qry) {
    echo "Submitted Successfully";
} else {
    echo "Error While Submit";
}

$pdo = null;
