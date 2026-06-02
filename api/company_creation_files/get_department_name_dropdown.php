<?php

/** Department Dropdown List **
 * Purpose:
 * - Fetches active departments for dropdown selection.
 * - Returns company-mapped departments for feedback screens.
 * - Returns all active departments for company creation screens.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$screen     = $_POST['screen'] ?? '';
$company_id = $_POST['company_id'] ?? '';

$result = [];

if ($screen == 'feedback_screen') {

    $stmt = $pdo->prepare("SELECT
            dc.id,
            dc.department_name
        FROM department_creation dc
        JOIN company_department_mapping cdm ON dc.id = cdm.department_id
        WHERE dc.department_status = ?
        AND cdm.company_id = ?
        ORDER BY dc.department_name ASC
    ");

    $stmt->execute([0, $company_id]);
} elseif ($screen == 'company_creation') {

    $stmt = $pdo->prepare("SELECT
            id,
            department_name
        FROM department_creation
        WHERE department_status = ?
        ORDER BY department_name ASC
    ");

    $stmt->execute([0]);
}

if (isset($stmt)) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $result[] = [
            'id' => $row['id'],
            'department_name' => $row['department_name']
        ];
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
