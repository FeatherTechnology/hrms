<?php
//Also using in Loan_entry-Loan Calculation.
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM `scheme` WHERE id='$id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
