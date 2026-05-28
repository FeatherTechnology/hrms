<?php
require '../../ajaxconfig.php';

$response = array();

$company_id = $_POST['company_id'];

try {
    $qry = $pdo->query("SELECT id , shift_name FROM shift_creation WHERE company_id = '$company_id'");

    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;
echo json_encode($response);
