<?php

/** Staff Dropdown List **
 * Purpose:
 * - Fetches active staff records based on company and role.
 * - Returns staff ID, name, and staff type.
 * - Used for dropdown selection in user creation and management screens.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$role       = $_POST['role'];

$response = [];

try {

    $stmt = $pdo->prepare("SELECT
            id,
            staff_name,
            staff_type
        FROM staff_creation
        WHERE company_id = ?
        AND staff_type = ?
        AND status = ?
    ");

    $stmt->execute([
        $company_id,
        $role,
        1
    ]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null; // Close Connection

echo json_encode($response);
