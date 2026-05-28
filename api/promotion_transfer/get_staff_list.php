<?php
require "../../ajaxconfig.php";
require "../../moneyFormatIndia.php";
@session_start();

$user_id = $_SESSION['user_id'];

/* Get Filters */
$company_id = isset($_POST['params']['company_id'])   ? $_POST['params']['company_id']  : '';
$department_id = isset($_POST['params']['department_id']) ? $_POST['params']['department_id'] : '';
$status = isset($_POST['params']['status']) ? $_POST['params']['status'] : '';   // 1 = Active, 2 = Inactive
$staff_id = isset($_POST['params']['staff_id']) ? $_POST['params']['staff_id'] : '';

$available_arr = [ '1' => 'Yes', '2' => 'No'];
$status_arr = [ '0'=>'Joining', '1' => 'Promotion', '2' => 'Transfer', '3' => 'Increment'];
$column = array(
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.joining_date',
    'sc.relieve_date',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'ti.team_name',
    'des.designation',
    'sc.id',
    'oc.pf_available',
    'oc.esi_available',
    'oc.pt_available',
    'oc.total_ctc',
    'oc.effective_from',
);


$query = "SELECT 
            sc.id,
            sc.staff_id,
            sc.staff_name,
            sc.joining_date,
            sc.relieve_date,

            cc.company_name,
            bc.branch_name,
            d.department_name,
            des.designation,
            ti.team_name,

            sc.mobile1,

            oc.total_ctc,
            oc.pf_available,
            oc.esi_available,
            oc.pt_available,
            oc.occ_status,
            oc.created_on,
            oc.effective_from,

            rp.staff_name AS reporting_person_name

          FROM staff_creation sc

          LEFT JOIN company_creation cc 
                 ON sc.company_id = cc.id

          LEFT JOIN occupation_info oc 
                 ON oc.staff_profile_id = sc.id

          LEFT JOIN branch_creation bc 
                 ON oc.branch_id = bc.id

          LEFT JOIN department_creation d 
                 ON oc.department = d.id

          LEFT JOIN team_name_creation ti 
                 ON oc.team = ti.id

          LEFT JOIN designation_creation des 
                 ON oc.designation = des.id

          LEFT JOIN staff_creation rp
                 ON oc.reporting_person = rp.id

          WHERE sc.id = '$staff_id'
";


/* Status Filter */
if ($status != '') {

    $query .= " AND sc.status = '$status' ";
}


/* Company Filter */
if ($company_id != '') {

    $query .= " AND sc.company_id = '$company_id' ";
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

    // latest occupation entries first
    $query .= " ORDER BY oc.id DESC ";
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
    $sub_array[] = (!empty($row['joining_date']) && $row['joining_date'] != '0000-00-00')  ? date('d-m-Y', strtotime($row['joining_date']))  : '';
    $sub_array[] = (!empty($row['relieve_date']) && $row['relieve_date'] != '0000-00-00')  ? date('d-m-Y', strtotime($row['relieve_date']))  : '';
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['team_name'];
    $sub_array[] = $row['designation'];
    $sub_array[] = $row['reporting_person_name'];
    $sub_array[] = isset($available_arr[$row['pf_available']]) ? $available_arr[$row['pf_available']]  : '';
    $sub_array[] = isset($available_arr[$row['esi_available']])  ? $available_arr[$row['esi_available']] : '';
    $sub_array[] = isset($available_arr[$row['pt_available']])  ? $available_arr[$row['pt_available']]    : '';
    $sub_array[] = moneyFormatIndia($row['total_ctc']);
    $sub_array[] = (!empty($row['effective_from']) && $row['effective_from'] != 'null')  ? date('d-m-Y', strtotime($row['effective_from']))  : '';
    // Occupation Status
    $sub_array[] = isset($status_arr[$row['occ_status']])  ? $status_arr[$row['occ_status']]  : '';
    $data[] = $sub_array;
}


/* Count Total */
function count_all_data($pdo)
{
    $stmt = $pdo->query(" SELECT COUNT(*)  FROM occupation_info");
return $stmt->fetchColumn();
}


/* Output */
$output = array(
    "draw"            => intval($_POST['draw']),
    "recordsTotal"    => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data"            => $data
);

echo json_encode($output);
?>

