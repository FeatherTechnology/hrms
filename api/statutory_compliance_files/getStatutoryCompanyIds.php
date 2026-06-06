<?php

/** Statutory Compliance Company List **
 * Purpose:
 * - Fetches unique company IDs from statutory compliance records.
 * - Returns company IDs in JSON format.
 */

require '../../ajaxconfig.php';

$response = [];

try {

    $stmt = $pdo->prepare("SELECT DISTINCT company_id FROM statutory_compliance");

    $stmt->execute();

    $response = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {

    $response = [
        'error' => $e->getMessage()
    ];
}

$pdo = null; // Close Connection

echo json_encode($response);
