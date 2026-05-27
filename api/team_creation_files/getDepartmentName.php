<?php
require '../../ajaxconfig.php';

$response = array();

$company_id = $_POST['company_name'];

try {

    $stmt = $pdo->prepare("SELECT 
            dc.id,
            dc.department_name
        FROM department_creation dc
        INNER JOIN company_department_mapping cdm 
            ON cdm.department_id = dc.id
        WHERE cdm.company_id = ?
        AND dc.department_status = 0
    ");

    $stmt->execute([$company_id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
