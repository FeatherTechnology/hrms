<?php

/** District List **
 * Purpose:
 * - Fetches all active districts for the selected state.
 * - Returns district data in JSON format for dropdown selection.
 */

require '../../ajaxconfig.php';

$state_id = $_POST['state_id'];

$response = [];

$stmt = $pdo->prepare("SELECT
        id,
        district_name
    FROM districts
    WHERE state_id = ?
    AND status = ?
");

$stmt->execute([$state_id, 1]);

if ($stmt->rowCount() > 0) {
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($response);
