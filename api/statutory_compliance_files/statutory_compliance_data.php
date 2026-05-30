<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = array();

$qry = $pdo->query("SELECT * FROM `statutory_compliance` WHERE id='$id'");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close connection.

echo json_encode($result);
