<?php

/** Fetch Statutory Compliance Details **
 * Purpose:
 * - Retrieves statutory compliance information based on the provided record ID.
 * - Returns statutory compliance details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT * FROM statutory_compliance WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
