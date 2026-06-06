<?php

/** Fetch Company Policy Details **
 * Purpose:
 * - Retrieves company policy information based on the provided company ID.
 * - Fetches maximum permission count and configured week-offs.
 * - Returns company policy details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        cp.max_permission,
        cw.week_off,
        cw.week_day
    FROM company_policies cp
    LEFT JOIN company_weekoffs cw
        ON cp.id = cw.company_policies_id
    WHERE cp.company_id = ?
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
