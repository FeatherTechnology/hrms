<?php

/** Fetch Shift Details **
 * Purpose:
 * - Retrieves shift information based on the provided shift ID.
 * - Returns shift details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT * FROM shift_creation WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
