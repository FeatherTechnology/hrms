<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$response = [];

try {

$stmt = $pdo->prepare("
    SELECT
        bc.id,
        bc.branch_name
    FROM branch_creation bc
    WHERE bc.company_id = ?
");

    $stmt->execute([$company_id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null; // Close Connection

echo json_encode($response);
