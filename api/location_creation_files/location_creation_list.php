<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

/* Get Filters */
$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

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

/* Main Query */
$query = "SELECT oi.id, sc.staff_id, sc.staff_name, dc.department_name, bc.branch_name, bcs.branch_name AS assigned_branch_name, lam.from_date, lam.to_date, lam.no_of_days,
        lam.lattitude_longitude , oi.staff_profile_id
        FROM occupation_info oi
        LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
        LEFT JOIN department_creation dc ON oi.department = dc.id 
        LEFT JOIN staff_creation sc ON oi.staff_profile_id = sc.id
        LEFT JOIN location_access_mapping lam ON lam.id = (SELECT id FROM location_access_mapping WHERE staff_profile_id = oi.staff_profile_id AND status = 0
        AND (CURDATE() BETWEEN from_date AND to_date OR from_date >= CURDATE())
        ORDER BY
        CASE
            WHEN CURDATE() BETWEEN from_date AND to_date THEN 0
            ELSE 1
        END,
        from_date ASC LIMIT 1)
        LEFT JOIN branch_creation bcs ON lam.assigned_branch = bcs.id
        LEFT JOIN users u ON u.id = '$user_id'
        WHERE oi.off_type = 1 AND oi.id IN (SELECT MAX(id) FROM occupation_info GROUP BY staff_profile_id) AND oi.reporting_person = u.staff_name_id AND (date(sc.relieve_date)>='$today' OR sc.relieve_date ='')";


/* Company Filter */
if ($company_id != '') {
    $query .= " AND oi.company_id = '$company_id' ";
}

/* Branch Filter */
if ($branch_id != '') {
    $query .= " AND oi.branch_id = '$branch_id' ";
}

/* Department Filter */
if ($department_id != '') {
    $query .= " AND oi.department = '$department_id' ";
}

/* Search */
if (isset($_POST['search']) && $_POST['search'] != "") {

    $search = $_POST['search'];

    $query .= " AND (
        sc.staff_id LIKE '$search%'
        OR sc.staff_name LIKE '%$search%'
        OR dc.department_name LIKE '%$search%'
        OR bc.branch_name LIKE '%$search%'
        OR bcs.branch_name LIKE '%$search%'
    )";
}

$query .= " GROUP BY oi.id ";

/* Order */
if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . " " . $_POST['order']['0']['dir'];
} else {
    $query .= " ORDER BY sc.id DESC ";
}

/* Limit */
$query1 = '';
if ($_POST['length'] != -1) {
    $query1 = " LIMIT " . $_POST['start'] . "," . $_POST['length'];
}

/* Execute */
$statement = $pdo->prepare($query);
$statement->execute();
$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);
$statement->execute();

$result = $statement->fetchAll();

$data = [];
$sno = $_POST['start'] + 1;

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
    $sub_array[] = "<span class='icon-border_color locationActionBtn' data-id='" . $row['id'] . "'data-staff-profile-id='" . $row['staff_profile_id'] . "'></span>";;

    $data[] = $sub_array;
}

/* Count Total */
function count_all_data($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM occupation_info WHERE off_type = 1");
    return $stmt->fetchColumn();
}

/* Output */
$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
);

echo json_encode($output);
