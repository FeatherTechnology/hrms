<?php
require '../../ajaxconfig.php';

$response = array();

try {
    $qry = $pdo->query("SELECT id , company_name FROM company_creation");

    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;
echo json_encode($response);
