<?php

/** Staff Dropdown List **
 * Purpose:
 * - Fetches active staff records based on company and role.
 * - Returns staff ID, name, and staff type.
 * - Used for dropdown selection in user creation and management screens.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$response = [];

try {

$stmt = $pdo->prepare("
    SELECT
        sc.id,
        sc.staff_name,
        sc.staff_type,

        CASE
            WHEN u.staff_name_id IS NOT NULL THEN 1
            ELSE 0
        END AS already_exists

    FROM staff_creation sc

    LEFT JOIN users u
        ON u.staff_name_id = sc.id

    WHERE sc.company_id = ?
    AND sc.status = ?
");

    $stmt->execute([
        $company_id,
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
