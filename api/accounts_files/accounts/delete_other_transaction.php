<?php
require '../../../ajaxconfig.php';

$id = $_POST['id'];
$qry = $pdo->query("DELETE FROM `other_transaction` WHERE id='$id'");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}

$pdo = null; //Close connection.

echo json_encode($result);
