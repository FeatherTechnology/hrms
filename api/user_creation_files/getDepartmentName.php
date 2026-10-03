<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$response = [];

try {

$stmt = $pdo->prepare("
    SELECT
        dc.id,
        dc.department_name
    FROM company_department_mapping cdm
    LEFT JOIN department_creation dc on dc.id = cdm.department_id
    WHERE cdm.company_id = ?
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
