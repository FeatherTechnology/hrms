<?php

/** Fetch Poll Title **
 * Purpose:
 * - Retrieves the poll title based on the provided poll ID.
 * - Returns poll title data in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT poll_title FROM poll_titles WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
