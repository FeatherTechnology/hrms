<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$response = array();

try {

    $stmt = $pdo->prepare("SELECT lam.from_date, lam.to_date, lam.assigned_branch, lam.lattitude_longitude, lam.reason, cc.id as company_id
        FROM location_access_mapping lam
        LEFT JOIN branch_creation bc ON lam.assigned_branch = bc.id 
        LEFT JOIN company_creation cc ON bc.company_id = cc.id
        WHERE lam.id = ?");

    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
