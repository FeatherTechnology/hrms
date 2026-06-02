<?php

/** Company Dropdown List **
 * Purpose:
 * - Fetches all companies.
 * - Returns company ID and company name for dropdown selection.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$response = [];

try {

    $stmt = $pdo->prepare("SELECT
            id,
            company_name
        FROM company_creation
        ORDER BY company_name ASC
    ");

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null; // Close Connection

echo json_encode($response);
