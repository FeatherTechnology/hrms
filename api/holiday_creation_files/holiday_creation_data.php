<?php

/** Fetch Holiday Details **
 * Purpose:
 * - Retrieves holiday information based on the provided holiday ID.
 * - Returns holiday details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT * FROM holiday_creation WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
