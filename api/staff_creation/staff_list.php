<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];

/* Get Filters */
$company_id = isset($_POST['params']['company_id'])   ? $_POST['params']['company_id']  : '';
$branch_id = isset($_POST['params']['branch_id']) ? $_POST['params']['branch_id'] : '';
$department_id = isset($_POST['params']['department_id']) ? $_POST['params']['department_id'] : '';
$status = isset($_POST['params']['status']) ? $_POST['params']['status'] : '';   // 1 = Active, 2 = Inactive

$staff_type = [1 => 'Employer', 2 => 'Employee'];
$today = date('Y-m-d');

$column = array(
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.staff_type',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'ti.team_name',
    'des.designation',
    'sc.mobile1'
);

/* Main Query */
$query = "SELECT 
            sc.id,
            sc.staff_id,
            sc.staff_name,
            sc.staff_type,
            cc.company_name,
            bc.branch_name,
            d.department_name,
            des.designation,
            ti.team_name,
            sc.mobile1

          FROM staff_creation sc

          LEFT JOIN company_creation cc 
                 ON sc.company_id = cc.id

          INNER JOIN (
                SELECT *
                FROM occupation_info oi1
                WHERE oi1.id = (
                    SELECT MAX(oi2.id)
                    FROM occupation_info oi2
                    WHERE oi2.staff_profile_id = oi1.staff_profile_id
                )
          ) oc ON oc.staff_profile_id = sc.id

          LEFT JOIN branch_creation bc 
                 ON oc.branch_id = bc.id

          LEFT JOIN department_creation d 
                 ON oc.department = d.id

          LEFT JOIN team_name_creation ti 
                 ON oc.team = ti.id

          LEFT JOIN designation_creation des 
                 ON oc.designation = des.id

          WHERE 1=1 ";


/* Status Filter */
/* Status Filter */
if ($status != '') {

    if ($status == 1) {

        // ACTIVE: still working OR no relieve date
        $query .= " AND sc.status = 1 
                    AND (
                        date(sc.relieve_date) >= '$today'
                        OR sc.relieve_date IS NULL
                        OR sc.relieve_date = ''
                    )";

    } elseif ($status == 2) {

        // INACTIVE: already relieved
        $query .= " AND (
                        sc.status = 2 
                        OR (
                            date(sc.relieve_date) < '$today'
                            AND sc.relieve_date != ''
                        )
                    )";
    }
}

/* Company Filter */
if ($company_id != '') {
    $query .= " AND sc.company_id = '$company_id' ";
}

/* Branch Filter */
if ($branch_id != '') {
    $query .= " AND oc.branch_id = '$branch_id' ";
}

/* Department Filter */
if ($department_id != '') {
    $query .= " AND oc.department = '$department_id' ";
}

/* Search */
if (isset($_POST['search']) && $_POST['search'] != "") {

    $search = $_POST['search'];

    $query .= " AND (
        sc.staff_id LIKE '$search%'
        OR sc.staff_name LIKE '%$search%'
        OR sc.staff_type LIKE '%$search%'
        OR sc.mobile1 LIKE '%$search%'
        OR cc.company_name LIKE '%$search%'
        OR bc.branch_name LIKE '%$search%'
        OR d.department_name LIKE '%$search%'
        OR ti.team_name LIKE '%$search%'
        OR des.designation LIKE '%$search%'
    )";
}


/* Order */
if (isset($_POST['order'])) {
    $query .= " ORDER BY " .
        $column[$_POST['order']['0']['column']] . " " .
        $_POST['order']['0']['dir'];
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
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['team_name'];
    $sub_array[] = $row['designation'];
    $sub_array[] = $row['mobile1'];

    $sub_array[] =
        "<span class='icon-border_color staffEditBtn'
        value='" . $row['id'] . "'></span>";

    $data[] = $sub_array;
}


/* Count Total */
function count_all_data($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM staff_creation");
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
