<?php

/** Fetch User Details **
 * Purpose:
 * - Retrieves user information based on the provided user ID.
 * - Fetches company, staff, branch, department, team, and designation details.
 * - Returns user details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        u.user_name,
        u.user_type,
        u.director_company,
        u.director_name,
        u.password,
        u.download_access,
        u.report_access,
        u.home_access,
        u.staff_id,
        u.allowed_request_type,
        u.approval_required,
        u.approved_request_type,
        cc.id AS company_id,
        sc.id AS staff_name,
        bc.branch_name,
        dc.department_name,
        tnc.team_name,
        ds.designation
    FROM users u
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = u.staff_name_id
        )
    LEFT JOIN branch_creation bc ON oi.branch_id = bc.id
    LEFT JOIN department_creation dc ON oi.department = dc.id
    LEFT JOIN team_name_creation tnc ON oi.team = tnc.id
    LEFT JOIN designation_creation ds ON oi.designation = ds.id
    LEFT JOIN company_creation cc ON u.company_id = cc.id
    LEFT JOIN staff_creation sc ON u.staff_name_id = sc.id
    WHERE u.id = ?
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
