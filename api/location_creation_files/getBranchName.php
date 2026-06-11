<?php

/** Branch Dropdown List **
 * Purpose:
 * - Fetches all branches for the selected company.
 * - Returns branch ID and branch name.
 * - Used for dropdown selection screens.
 */

require '../../ajaxconfig.php';

$response = [];

$company_id = $_POST['company_id'];

try {

    $stmt = $pdo->prepare("SELECT id, branch_name FROM branch_creation WHERE company_id = ?");

    $stmt->execute([$company_id]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null; // Close Connection

echo json_encode($response);
