<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$role = $_POST['role'];

$response = array();

try {

    $qry = $pdo->query("SELECT id, staff_name, staff_type FROM staff_creation WHERE company_id = '$company_id' AND staff_type = '$role' AND status = 1");

    if ($qry->rowCount() > 0) {

        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
