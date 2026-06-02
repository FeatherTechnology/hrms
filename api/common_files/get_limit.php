<?php

/** License Limits **
 * Purpose:
 * - Fetches configured license limits.
 * - Returns branch and company limits.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$stmt = $pdo->prepare("SELECT *
    FROM license_limits
");

$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$pdo = null; // Close Connection

echo json_encode([
    'branch_limit'  => $row['branch_limit'] ?? 0,
    'company_limit' => $row['company_limit'] ?? 0
]);
