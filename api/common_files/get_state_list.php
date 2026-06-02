<?php

/** State List **
 * Purpose:
 * - Fetches all active states.
 * - Returns state data in JSON format for dropdown selection.
 */

require '../../ajaxconfig.php';

$response = [];

$stmt = $pdo->prepare("SELECT
        id,
        state_name
    FROM states
    WHERE status = ?
");

$stmt->execute([1]);

if ($stmt->rowCount() > 0) {
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($response);
