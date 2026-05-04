<?php
require '../../ajaxconfig.php';
$qry = $pdo->query("SELECT * FROM `line_name_creation`");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
