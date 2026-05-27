<?php
require '../../ajaxconfig.php';

$response = array();

$company_id = $_POST['company_id'];
$shift_id   = $_POST['shift_id'];

try {

    $qry = $pdo->query("SELECT oi.staff_profile_id as id , sc.staff_name
    FROM staff_creation sc
    LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = sc.id)
    WHERE oi.company_id = '$company_id' AND oi.shift = '$shift_id'");

    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
