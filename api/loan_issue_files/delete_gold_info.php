<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$result = 0;
$qry = $pdo->query("DELETE FROM `gold_info` WHERE id='$id'");
if ($qry) {
    $result = 1;
} else {
    $result = 2;
}
$pdo = null; //Close connection.
echo json_encode($result);
