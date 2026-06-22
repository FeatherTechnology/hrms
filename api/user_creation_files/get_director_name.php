<?php
include '../../ajaxconfig.php';

$result = array();
$qry = $pdo->query("SELECT director_name ,id FROM director_creation ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
