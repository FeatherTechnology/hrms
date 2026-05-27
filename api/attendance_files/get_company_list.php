<?php
include '../../ajaxconfig.php';

$result = array();
$qry = $pdo->query("SELECT company_name ,id FROM company_creation where 1 ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //close connection.
echo json_encode($result);
