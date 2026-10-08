<?php

/** Department Dropdown List **
 * Purpose:
 * - Fetches active departments for dropdown selection.
 * - For feedback/performance screens:
 *      user_type = 1 → all departments for company
 *      user_type = 2 → mapped departments
 *                    → if department mapping is empty, use branch mapping
 * - Returns all active departments for company creation screens.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';
@session_start();

$screen     = $_POST['screen'] ?? '';
$company_id = $_POST['company_id'] ?? '';

$result = [];

if ($screen == 'feedback_screen' || $screen == 'performance_analysis') {

    // Get logged-in user's type, department mapping and branch mapping
    $userid = $_SESSION['user_id'];

    $userStmt = $pdo->prepare("
        SELECT 
            user_type,
            mapping_department,
            mapping_branch
        FROM users
        WHERE id = ?
    ");

    $userStmt->execute([$userid]);

    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    $user_type          = $userData['user_type'] ?? 0;
    $mapping_department = trim($userData['mapping_department'] ?? '');
    $mapping_branch     = trim($userData['mapping_branch'] ?? '');

    if ($user_type == 1) {

        // User type 1 = Director
        // Get all active departments mapped to the company

        $stmt = $pdo->prepare("
            SELECT
                dc.id,
                dc.department_name
            FROM department_creation dc
            JOIN company_department_mapping cdm
                ON dc.id = cdm.department_id
            WHERE dc.department_status = ?
              AND cdm.company_id = ?
            ORDER BY dc.department_name ASC
        ");

        $stmt->execute([0, $company_id]);

    } elseif ($user_type == 2) {

        // User type 2 = Staff

        if (!empty($mapping_department)) {

            // Department mapping available
            // Show only mapped departments

            $mapping_department = str_replace(' ', '', $mapping_department);

            $stmt = $pdo->prepare("
                SELECT
                    dc.id,
                    dc.department_name
                FROM department_creation dc
                WHERE dc.department_status = ?
                  AND FIND_IN_SET(dc.id, ?) > 0
                ORDER BY dc.department_name ASC
            ");

            $stmt->execute([0, $mapping_department]);

        } elseif (!empty($mapping_branch)) {

            // Department mapping is empty
            // So use branch mapping

            $mapping_branch = str_replace(' ', '', $mapping_branch);

            $stmt = $pdo->prepare("
                SELECT DISTINCT
                    dc.id,
                    dc.department_name
                FROM department_creation dc
                JOIN company_department_mapping cdm
                    ON dc.id = cdm.department_id
                JOIN branch_creation bc
                    ON bc.company_id = cdm.company_id
                WHERE dc.department_status = ?
                  AND FIND_IN_SET(bc.id, ?) > 0
                  AND cdm.company_id = ?
                ORDER BY dc.department_name ASC
            ");

            $stmt->execute([0, $mapping_branch, $company_id]);

        } else {

            // No department mapping and no branch mapping
            $stmt = $pdo->query("
                SELECT
                    id,
                    department_name
                FROM department_creation
                WHERE 1 = 0
            ");
        }

    } else {

        // Invalid / no user type
        $stmt = $pdo->query("
            SELECT
                id,
                department_name
            FROM department_creation
            WHERE 1 = 0
        ");
    }

} elseif ($screen == 'company_creation') {

    // Company creation → all active departments

    $stmt = $pdo->prepare("
        SELECT
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