<?php

/** Fetch Branch Details **
 * Purpose:
 * - Retrieves branch information based on the provided branch ID.
 * - Fetches the associated company name.
 * - Returns branch details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        bc.*,
        cc.company_name
    FROM branch_creation bc
    JOIN company_creation cc
        ON bc.company_id = cc.id
    WHERE bc.id = ?
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
