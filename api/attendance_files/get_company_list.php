<?php
// to get the company list based o the user 
include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'] ?? "";

$result = array();
$qry = $pdo->query("SELECT cc.company_name , cc.id FROM company_creation cc LEFT JOIN users u on u.company_id = cc.id  where u.id= $userid ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
