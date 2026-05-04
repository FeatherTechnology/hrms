<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT ac.*, GROUP_CONCAT(acan.area_id) AS area_id
    FROM area_creation ac 
    LEFT JOIN area_creation_area_name acan ON ac.id = acan.area_creation_id  
    WHERE ac.id = '$id'
    GROUP BY ac.id");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close connection.

echo json_encode($result);
