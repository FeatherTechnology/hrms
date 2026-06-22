<?php

/** User List **
 * Purpose:
 * - Fetches users based on active/inactive status.
 * - Retrieves company, staff, branch, department, team, and designation details.
 * - Converts role values into display names.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns user data in JSON format for DataTable/Grid display.
 */

require "../../ajaxconfig.php";

$status = $_POST['status'] ?? '';
$company_id = $_POST['company_id'] ?? '';
$user_type = $_POST['user_type'] ?? '';

$user_arr = [];

$status_value = ($status == 'active') ? 0 : 1;

$sql = "SELECT
        u.id,
        u.user_name AS user_id,
        sc.staff_name,
        bc.branch_name,
        dc.department_name,
        tnc.team_name,
        ds.designation,
        u.user_type,
        u.company_id,
        u.director_company,
        drc.director_name
    FROM users u
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = u.staff_name_id
        )
    LEFT JOIN director_creation drc ON drc.id = u.director_name
    LEFT JOIN branch_creation bc ON oi.branch_id = bc.id
    LEFT JOIN department_creation dc ON oi.department = dc.id
    LEFT JOIN team_name_creation tnc ON oi.team = tnc.id
    LEFT JOIN designation_creation ds ON oi.designation = ds.id
    LEFT JOIN company_creation cc ON u.company_id = cc.id
    LEFT JOIN staff_creation sc ON u.staff_name_id = sc.id
    WHERE u.status = ?";

$params = [$status_value];


// 🔥 COMPANY + USER TYPE FILTER
if (!empty($company_id) && !empty($user_type)) {

    if ($user_type == 1) {

        // Director company (comma-separated)
        $sql .= " AND FIND_IN_SET(?, u.director_company)";
        $params[] = $company_id;

    } elseif ($user_type == 2) {

        // Normal company match
        $sql .= " AND u.company_id = ?";
        $params[] = $company_id;
    }
}

$sql .= " GROUP BY u.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if ($stmt->rowCount() > 0) {

   while ($user_info = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Get company names for directors
    if (!empty($user_info['director_company'])) {

        $companyIds = explode(',', $user_info['director_company']);

        $placeholders = implode(',', array_fill(0, count($companyIds), '?'));

        $companyStmt = $pdo->prepare("
            SELECT company_name
            FROM company_creation
            WHERE id IN ($placeholders)
        ");

        $companyStmt->execute($companyIds);

        $companyNames = $companyStmt->fetchAll(PDO::FETCH_COLUMN);

        $user_info['company_names'] = implode(', ', $companyNames);
    } else {
        $user_info['company_names'] = '';
    }

    // Action buttons
    if ($status_value == 0) {
        $user_info['action'] = "
            <span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "'></span>
            <span class='icon-trash-2 userDeleteBtn' value='" . $user_info['id'] . "'></span>
        ";
    } else {
        $user_info['action'] = "
            <span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "' style='pointer-events:none;opacity:0.5;cursor:not-allowed;'></span>
        ";
    }

    $user_arr[] = $user_info;
}
}

$pdo = null;

echo json_encode($user_arr);
