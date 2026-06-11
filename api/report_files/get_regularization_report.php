<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];


$userQry = $pdo->query("SELECT report_access FROM users WHERE id = $user_id ");
$rowuser = $userQry->fetch();
$report_access = $rowuser['report_access'];

$whereCondition = '';

if ($report_access == '2') {
    $whereCondition = " r.insert_user_id = '$user_id' AND ";
}
$from_date    = $_POST['params']['from_date'] ?? '';
$to_date      = $_POST['params']['to_date'] ?? '';
$company_id   = $_POST['params']['company_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$status       = $_POST['params']['status'] ?? '';
$staff_arr = [1 => 'Employer', 2 => 'Employee'];
$req_type = [1 => 'Leave', 2 => 'Permission', 3 => 'Week Off', 4 => 'OT'];
$reg_status = [0 => 'Pending', 1 => 'Approved', 2 => 'Cancel'];



$column = array(
    'r.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.staff_type',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'des.designation',
    'ti.team_name',
    'r.req_type',   
    'lc.leave_type',
    'r.req_date',
    'r.from_date',
    'r.to_date',
    'r.total_min',
    'r.reason',
    'sc.id',
    'r.approved_from_date',
    'r.approved_to_date',
    'r.approved_total_min',
    'r.updated_date',
    'r.remarks',
    'r.status'

);

/* ================= MAIN QUERY ================= */

$query = "
SELECT
    r.id,

    sc.staff_id,
    sc.staff_name,
    sc.staff_type,

    cc.company_name,

    bc.branch_name,
    d.department_name,
    des.designation,
    ti.team_name,

    r.req_type,
    lc.leave_type,
    r.req_date,
    r.from_date,
    r.to_date,
    r.approved_from_date,
    r.approved_to_date,
    r.total_min,
    r.approved_total_min,
    r.reason,
    r.remarks,
    r.status,
    r.updated_date,
    r.updated_login_id,

    approver_sc.staff_name AS approver_name,
    reporting_sc.staff_name AS assigned_to

FROM regularization r

LEFT JOIN staff_creation sc
    ON r.staff_profile_id = sc.id
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
) oc
    ON oc.staff_profile_id = sc.id
LEFT JOIN branch_creation bc
    ON oc.branch_id = bc.id
LEFT JOIN department_creation d
    ON oc.department = d.id
LEFT JOIN team_name_creation ti
    ON oc.team = ti.id
LEFT JOIN designation_creation des
    ON oc.designation = des.id
LEFT JOIN leave_creation lc
    ON r.leave_type = lc.id
LEFT JOIN users u
    ON u.id = r.updated_login_id
LEFT JOIN staff_creation approver_sc
    ON approver_sc.id = u.staff_name_id
LEFT JOIN staff_creation reporting_sc
    ON reporting_sc.id = oc.reporting_person

WHERE 1 = 1
";

/* ================= FILTERS ================= */
$query .= $whereCondition;
/* Date Filter */
if (!empty($from_date) && !empty($to_date)) {

    $query .= "  AND (DATE(r.from_date) <= '$to_date' AND DATE(r.to_date) >= '$from_date') ";
}
/* Company Filter */
if (!empty($company_id)) {

    $query .= " AND r.company_id = '$company_id' ";
}

/* Department Filter */
if (!empty($department_id)) {

    $query .= " AND r.dep_id = '$department_id' ";
}

/* Status Filter
DB:
0 = Pending
1 = Approved
2 = Cancelled
*/
if ($status !== '') {

    $query .= " AND r.status = '$status' ";
}

/* ================= SEARCH ================= */

if (isset($_POST['search']['value']) && $_POST['search']['value'] != '') {

    $search = $_POST['search']['value'];

    $query .= "
    AND (
        sc.staff_id LIKE '%$search%'
        OR sc.staff_name LIKE '%$search%'
        OR cc.company_name LIKE '%$search%'
        OR bc.branch_name LIKE '%$search%'
        OR d.department_name LIKE '%$search%'
        OR ti.team_name LIKE '%$search%'
        OR des.designation LIKE '%$search%'
        OR r.req_date LIKE '%$search%'

    )";
}

/* ================= ORDER ================= */

if (isset($_POST['order'])) {

    $query .= " ORDER BY " .
        $column[$_POST['order'][0]['column']] . " " .
        $_POST['order'][0]['dir'];
} else {

    $query .= " ORDER BY r.id DESC ";
}

/* ================= PAGINATION ================= */

$query1 = '';

if ($_POST['length'] != -1) {

    $query1 = " LIMIT " .
        $_POST['start'] . "," .
        $_POST['length'];
}

/* ================= RECORD COUNT ================= */

$statement = $pdo->prepare($query);
$statement->execute();
$number_filter_row = $statement->rowCount();

/* ================= DATA ================= */

$statement = $pdo->prepare($query . $query1);
$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = array();
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    $isTimeBased = in_array($row['req_type'], [2, 4]);

    $req_from_date = date(
        $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
        strtotime($row['from_date'])
    );

    $req_to_date = date(
        $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
        strtotime($row['to_date'])
    );

    $approved_from_date = !empty($row['approved_from_date']) &&
        $row['approved_from_date'] != '0000-00-00 00:00:00'
        ? date(
            $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
            strtotime($row['approved_from_date'])
        )
        : '';

    $approved_to_date = !empty($row['approved_to_date']) &&
        $row['approved_to_date'] != '0000-00-00 00:00:00'
        ? date(
            $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
            strtotime($row['approved_to_date'])
        )
        : '';

    $cancelled_date = ($row['status'] == 2 && !empty($row['updated_date']))
        ? date('d-m-Y', strtotime($row['updated_date']))
        : '';
    $requested_days = formatDuration($row['total_min']);
    $approved_days  = formatDuration($row['approved_total_min']);

    $sub_array = [];

    // Common Columns
    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $staff_arr[$row['staff_type']] ?? '';
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['designation'];
    $sub_array[] = $req_type[$row['req_type']] ?? '';
    $sub_array[] = $row['leave_type'] ?? '';
    $sub_array[] = date('d-m-Y', strtotime($row['req_date']));
    $sub_array[] = $req_from_date;
    $sub_array[] = $req_to_date;
    $sub_array[] = $requested_days;
    $sub_array[] = $row['reason'];

    // Pending
    if ($row['status'] == 0) {

        $sub_array[] = $row['assigned_to'];
        $sub_array[] = 'Pending';
    }

    // Approved
    else if ($row['status'] == 1) {

        $sub_array[] = $row['approver_name'];
        $sub_array[] = $approved_from_date;
        $sub_array[] = $approved_to_date;
        $sub_array[] = $approved_days;
        $sub_array[] = $row['remarks'];
        $sub_array[] = 'Approved';
    }

    // Cancelled
    else if ($row['status'] == 2) {

        $sub_array[] = $row['approver_name'];
        $sub_array[] = $cancelled_date;
        $sub_array[] = $row['remarks'];
        $sub_array[] = 'Cancelled';
    }

    $data[] = $sub_array;
}
/* ================= TOTAL COUNT ================= */

function count_all_data($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM regularization");
    return $stmt->fetchColumn();
}

/* ================= OUTPUT ================= */

$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
);

echo json_encode($output);


function formatDuration($minutes)
{
    if (empty($minutes)) {
        return '0 Minutes';
    }

    $days = floor($minutes / 1440); // 24 * 60
    $hours = floor(($minutes % 1440) / 60);
    $mins = $minutes % 60;

    return "{$days} Days {$hours} Hours {$mins} Minutes";
}
