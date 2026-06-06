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

$status = $_POST['status'];

$user_arr = [];

$status_value = ($status == 'active') ? 0 : 1;

$stmt = $pdo->prepare("SELECT
        u.id,
        u.user_name AS user_id,
        cc.company_name,
        u.role,
        sc.staff_name,
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
    WHERE u.status = ?
    GROUP BY u.id
");

$stmt->execute([$status_value]);

if ($stmt->rowCount() > 0) {

    while ($user_info = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Role
        if ($user_info['role'] == 1) {
            $user_info['role'] = 'Employer';
        } elseif ($user_info['role'] == 2) {
            $user_info['role'] = 'Employee';
        }

        // Action Button
        if ($status_value == 0) {
            $user_info['action'] = "
                <span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "'></span>
                <span class='icon-trash-2 userDeleteBtn' value='" . $user_info['id'] . "'></span>
            ";
        } else {
            $user_info['action'] = "<span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "'></span>";
        }

        $user_arr[] = $user_info;
    }
}

$pdo = null; // Close Connection

echo json_encode($user_arr);
