<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

/* Get Filters */
$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

// 1. Fetch current logged-in user's role hierarchy context
$user_stmt = $pdo->prepare("
    SELECT 
        dc.designation_level,
        u.user_type
    FROM users u
    LEFT JOIN occupation_info oi ON oi.id = (
        SELECT MAX(id)
        FROM occupation_info
        WHERE staff_profile_id = u.staff_name_id
    )
    LEFT JOIN designation_creation dc ON dc.id = oi.designation
    WHERE u.id = ?
");
$user_stmt->execute([$user_id]);
$current_user = $user_stmt->fetch();

$my_level     = $current_user['designation_level'] ?? 0;
$user_type  = $current_user['user_type'] ?? 0;

/* Column definitions for DataTables indexing */
$column = array(
    'oi.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'bc.branch_name',
    'bcs.branch_name',
    'lam.from_date',
    'lam.to_date',
    'lam.no_of_days',
    'lam.lattitude_longitude',
    'oi.id'
);

/* Base Query Conditions (Shared between Data and Counts) */
// NOTE: Added LEFT JOINs to fetch the designation level of the target staff's reporting person
$base_query = " FROM occupation_info oi
                LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
                LEFT JOIN department_creation dc ON oi.department = dc.id 
                LEFT JOIN staff_creation sc ON oi.staff_profile_id = sc.id
                LEFT JOIN designation_creation des ON oi.designation = des.id
                LEFT JOIN location_access_mapping lam ON lam.id = (
                    SELECT id FROM location_access_mapping 
                    WHERE staff_profile_id = oi.staff_profile_id AND status = 0
                    AND (CURDATE() BETWEEN from_date AND to_date OR from_date >= CURDATE())
                    ORDER BY CASE WHEN CURDATE() BETWEEN from_date AND to_date THEN 0 ELSE 1 END, from_date ASC LIMIT 1
                )
                LEFT JOIN branch_creation bcs ON lam.assigned_branch = bcs.id
                WHERE oi.off_type = 1 
                AND oi.id IN (SELECT MAX(id) FROM occupation_info GROUP BY staff_profile_id) 
                AND (DATE(sc.relieve_date) >= '$today' OR sc.relieve_date = '' OR sc.relieve_date IS NULL)";
                
if ($user_type == 2) {

    $base_query .= " AND (
            des.designation_level > " . intval($my_level) . "
    ) ";
}

/* Apply Form Dropdown Filters */
if ($company_id != '') {
    $base_query .= " AND oi.company_id = " . intval($company_id);
}
if ($branch_id != '') {
    $base_query .= " AND oi.branch_id = " . intval($branch_id);
}
if ($department_id != '') {
    $base_query .= " AND oi.department = " . intval($department_id);
}

/* Total Base Records Count (Permission-Scoped Total) */
$total_stmt = $pdo->query("SELECT COUNT(oi.id) " . $base_query);
$total_records = $total_stmt->fetchColumn();

/* Apply DataTables Global Text Search */
if (isset($_POST['search']['value']) && $_POST['search']['value'] != "") {
    $search = $pdo->quote($_POST['search']['value']);
    $search_val = trim($search, "'");

    $base_query .= " AND (
        sc.staff_id LIKE '$search_val%'
        OR sc.staff_name LIKE '%$search_val%'
        OR dc.department_name LIKE '%$search_val%'
        OR bc.branch_name LIKE '%$search_val%'
        OR bcs.branch_name LIKE '%$search_val%'
    )";
}

/* Get Filtered Row Count prior to applying limits */
$filter_stmt = $pdo->query("SELECT COUNT(oi.id) " . $base_query);
$number_filter_row = $filter_stmt->fetchColumn();

/* Order Configuration */
if (isset($_POST['order'])) {
    $base_query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . " " . $_POST['order']['0']['dir'];
} else {
    $base_query .= " ORDER BY sc.id DESC ";
}

/* Limit Configuration */
$limit = '';
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $limit = " LIMIT " . intval($_POST['start']) . "," . intval($_POST['length']);
}

/* Build and Execute Main Data Query */
$main_sql = "SELECT oi.id, sc.staff_id, sc.staff_name, dc.department_name, bc.branch_name, 
                    bcs.branch_name AS assigned_branch_name, lam.from_date, lam.to_date, lam.no_of_days,
                    lam.lattitude_longitude, oi.staff_profile_id, des.designation_level " . $base_query . $limit;


$statement = $pdo->query($main_sql);
$result = $statement->fetchAll();

$data = [];
$sno = intval($_POST['start'] ?? 0) + 1;

foreach ($result as $row) {
    $sub_array = array();
    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['assigned_branch_name'];
    $sub_array[] = isset($row['from_date']) ? date('d-m-Y', strtotime($row['from_date'])) : '';
    $sub_array[] = isset($row['to_date']) ? date('d-m-Y', strtotime($row['to_date'])) : '';
    $sub_array[] = $row['no_of_days'];
    $sub_array[] = $row['lattitude_longitude'];
    $sub_array[] = "<span class='icon-border_color locationActionBtn' data-id='" . $row['id'] . "' data-staff-profile-id='" . $row['staff_profile_id'] . "'></span>";

    $data[] = $sub_array;
}

/* Output JSON structure */
$output = array(
    "draw"            => intval($_POST['draw'] ?? 0),
    "recordsTotal"    => intval($total_records),
    "recordsFiltered" => intval($number_filter_row),
    "data"            => $data
);

echo json_encode($output);
?>