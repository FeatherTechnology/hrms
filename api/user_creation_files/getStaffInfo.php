<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$response = array();

try {

    $stmt = $pdo->prepare("SELECT oi.staff_id, bc.branch_name, dc.department_name, tnc.team_name, ds.designation
        FROM occupation_info oi
        LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
        LEFT JOIN department_creation dc ON oi.department = dc.id 
        LEFT JOIN team_name_creation tnc ON oi.team = tnc.id 
        LEFT JOIN designation_creation ds ON oi.designation = ds.id
        WHERE oi.staff_profile_id = ?
        AND oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = ?)
    ");

    $stmt->execute([$id, $id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
