<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$response = array();

try {

    $stmt = $pdo->prepare("SELECT bc.branch_name, bc.company_id, dc.department_name, sc.staff_name, oi.staff_id
        FROM occupation_info oi
        LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
        LEFT JOIN department_creation dc ON oi.department = dc.id 
        LEFT JOIN staff_creation sc ON oi.staff_profile_id = sc.id
        WHERE oi.id = ?");

    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
