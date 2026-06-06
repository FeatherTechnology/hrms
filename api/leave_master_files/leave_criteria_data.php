<?php

/** Fetch Leave Details **
 * Purpose:
 * - Retrieves leave information based on the provided leave ID.
 * - Returns leave details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT * FROM leave_creation WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
