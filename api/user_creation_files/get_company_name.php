<?php

require '../../ajaxconfig.php';

$where = 1;

$result = [];

$stmt = $pdo->prepare("SELECT id, company_name FROM company_creation ");

$stmt->execute();

if (isset($stmt)) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = [
            'id' => $row['id'],
            'company_name' => $row['company_name']
        ];
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
