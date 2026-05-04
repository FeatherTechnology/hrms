<?php
include "../../../ajaxconfig.php";
session_start();
$user_id = $_SESSION['user_id'];

$bank_stmt_id = $_POST['bank_stmt_id'];
$response = '';

$qry = $pdo->query("UPDATE bank_clearance set clr_status = 1 where id = '$bank_stmt_id' "); // 1 means cleared

if ($qry) {
    $response = 0;
} else {
    $response = 1;
}
echo $response;
