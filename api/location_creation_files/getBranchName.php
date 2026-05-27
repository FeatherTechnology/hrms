<?php
require '../../ajaxconfig.php';

$response = array();

$compnay_id = $_POST['compnay_id'];

try {
    $qry = $pdo->query("SELECT id , branch_name FROM branch_creation WHERE company_id = '$compnay_id'");

    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;
echo json_encode($response);
