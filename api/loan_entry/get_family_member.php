<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, fam_name FROM  family_info");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);