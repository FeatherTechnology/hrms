<?php
require '../../ajaxconfig.php';

$response = [];

try {

    $qry = $pdo->query("
        SELECT DISTINCT company_id 
        FROM statutory_compliance
    ");

    $response = $qry->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {

    $response = ['error' => $e->getMessage()];
}

echo json_encode($response);
?>