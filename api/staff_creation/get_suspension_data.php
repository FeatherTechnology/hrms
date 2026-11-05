<?php
require '../../ajaxconfig.php';

$suspension_id = $_POST['suspension_id'];

$qry = $pdo->query("SELECT * FROM `staff_suspended` WHERE id='$suspension_id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);