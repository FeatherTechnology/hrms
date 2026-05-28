<?php
require '../../ajaxconfig.php';

$id = $_POST['branch_id'];

$response = array();

try {

    $stmt = $pdo->prepare("SELECT location FROM branch_creation WHERE id = ?");

    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
