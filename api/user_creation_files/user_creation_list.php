<?php

require "../../ajaxconfig.php";

$status = $_POST['status'];

$user_arr = array();

$status_value = ($status == 'active') ? 0 : 1;

$qry = $pdo->query("SELECT u.id, cc.company_name, u.role, sc.staff_name, bc.branch_name, dc.department_name, tnc.team_name, ds.designation
    FROM users u 
    LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = u.staff_name_id)
    LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
    LEFT JOIN department_creation dc ON oi.department = dc.id 
    LEFT JOIN team_name_creation tnc ON oi.team = tnc.id 
    LEFT JOIN designation_creation ds ON oi.designation = ds.id
    LEFT JOIN company_creation cc ON u.company_id = cc.id
    LEFT JOIN staff_creation sc ON u.staff_name_id = sc.id
    WHERE u.status = '$status_value'
    GROUP BY u.id
");

if ($qry->rowCount() > 0) {

    while ($user_info = $qry->fetch(PDO::FETCH_ASSOC)) {

        // role
        if ($user_info['role'] == 1) {

            $user_info['role'] = 'Employer';
        } else if ($user_info['role'] == 2) {

            $user_info['role'] = 'Employee';
        }

        $user_info['action'] = "
            <span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "'></span>

            <span class='icon-trash-2 userDeleteBtn' value='" . $user_info['id'] . "'></span>
        ";

        $user_arr[] = $user_info;
    }
}

$pdo = null;

echo json_encode($user_arr);
