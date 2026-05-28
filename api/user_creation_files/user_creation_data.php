<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT u.user_name , u.password, u.download_access, u.report_access, u.home_access, u.user_code, u.staff_id , cc.id as company_id, u.role, sc.id as staff_name,
        bc.branch_name, dc.department_name, tnc.team_name, ds.designation
        FROM users u 
        LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = u.staff_name_id)
        LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
        LEFT JOIN department_creation dc ON oi.department = dc.id 
        LEFT JOIN team_name_creation tnc ON oi.team = tnc.id 
        LEFT JOIN designation_creation ds ON oi.designation = ds.id
        LEFT JOIN company_creation cc ON u.company_id = cc.id
        LEFT JOIN staff_creation sc ON u.staff_name_id = sc.id
        WHERE u.id = $id");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close connection.
echo json_encode($result);
