<?php

/** Fetch Department Details **
 * Purpose:
 * - Retrieves department information based on the provided department ID.
 * - Returns department details in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT * FROM department_creation WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
