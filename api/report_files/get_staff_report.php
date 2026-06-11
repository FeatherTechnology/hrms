<?php
require "../../ajaxconfig.php";
require "../../moneyFormatIndia.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? '';

/* Filters */
$company_id    = $_POST['params']['company_id'] ?? '';
$branch_id     = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

/* Lookup Arrays */
$staff_type = [1 => 'Employer', 2 => 'Employee'];
$gender_type = [1 => 'Male', 2 => 'Female'];
$office_type = [1 => 'Office', 2 => 'Field'];
$branch_admin_type = [1 => 'Yes', 2 => 'No'];
$pf_available_type = [1 => 'Yes', 2 => 'No'];
$esi_available_type = [1 => 'Yes', 2 => 'No'];
$pt_available_type = [1 => 'Yes', 2 => 'No'];
$status_type = [1 => 'Active', 2 => 'In Active'];

/* Datatable Columns */
$column = array(
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.staff_type',
    'sc.gender',
    'sc.place',
    'sc.mobile1',
    'sc.email',
    'sc.joining_date',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'ti.team_name',
    'oc.off_type',
    'des.designation',
    'rp.staff_name',
    'oc.branch_admin',
    'sc.relieve_date',
    'oc.pf_available',
    'oc.esi_available',
    'oc.pt_available',
    'shc.shift_name',
    'oc.total_ctc',
    'oc.annual_ctc',
    'sc.status'
);

/* Base Query */
$baseQuery = "
FROM staff_creation sc

LEFT JOIN company_creation cc ON sc.company_id = cc.id

INNER JOIN (
    SELECT oi.*
    FROM occupation_info oi
    INNER JOIN (
        SELECT staff_profile_id, MAX(id) AS max_id
        FROM occupation_info
        GROUP BY staff_profile_id
    ) latest
    ON oi.id = latest.max_id
) oc
ON oc.staff_profile_id = sc.id

LEFT JOIN staff_creation rp ON rp.id = oc.reporting_person
LEFT JOIN branch_creation bc ON bc.id = oc.branch_id
LEFT JOIN department_creation d ON d.id = oc.department
LEFT JOIN team_name_creation ti ON ti.id = oc.team
LEFT JOIN designation_creation des ON des.id = oc.designation
LEFT JOIN shift_creation shc ON shc.id = oc.shift
WHERE 1=1
";

$params = [];

/* Filters */
if (!empty($company_id)) {
    $baseQuery .= " AND sc.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

if (!empty($branch_id)) {
    $baseQuery .= " AND oc.branch_id = :branch_id ";
    $params[':branch_id'] = $branch_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND oc.department = :department_id ";
    $params[':department_id'] = $department_id;
}

/* Search */
if (!empty($_POST['search'])) {

    $search = trim($_POST['search']);

    $baseQuery .= "
    AND (
        sc.staff_id LIKE :search
        OR sc.staff_name LIKE :search
        OR sc.mobile1 LIKE :search
        OR sc.email LIKE :search
        OR sc.place LIKE :search
        OR cc.company_name LIKE :search
        OR bc.branch_name LIKE :search
        OR d.department_name LIKE :search
        OR ti.team_name LIKE :search
        OR des.designation LIKE :search
        OR rp.staff_name LIKE :search
        OR shc.shift_name LIKE :search
    )";

    $params[':search'] = "%{$search}%";
}

/* Select Query */
$query = "
SELECT
    sc.id,
    sc.staff_id,
    sc.staff_name,
    sc.staff_type,
    sc.gender,
    sc.place,
    sc.mobile1,
    sc.email,
    sc.joining_date,
    cc.company_name,
    bc.branch_name,
    d.department_name,
    ti.team_name,
    oc.off_type,
    des.designation,
    rp.staff_name AS reporting_person,
    oc.branch_admin,
    sc.relieve_date,
    oc.pf_available,
    oc.esi_available,
    oc.pt_available,
    shc.shift_name,
    oc.total_ctc,
    oc.annual_ctc,
    sc.status
" . $baseQuery;

/* Filtered Count */
$stmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$stmt->execute($params);
$recordsFiltered = $stmt->fetchColumn();

/* Total Count */
$stmt = $pdo->query("SELECT COUNT(*) FROM staff_creation");
$recordsTotal = $stmt->fetchColumn();

/* Order */
if (isset($_POST['order'])) {

    $orderColumn = $column[$_POST['order'][0]['column']];
    $orderDir = ($_POST['order'][0]['dir'] == 'asc') ? 'ASC' : 'DESC';

    $query .= " ORDER BY {$orderColumn} {$orderDir}";
} else {
    $query .= " ORDER BY sc.id DESC";
}

/* Pagination */
if ($_POST['length'] != -1) {
    $query .= " LIMIT :start, :length";
}

$stmt = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

if ($_POST['length'] != -1) {
    $stmt->bindValue(':start', (int)$_POST['start'], PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $staff_type[$row['staff_type']] ?? '';
    $sub_array[] = $gender_type[$row['gender']] ?? '';
    $sub_array[] = $row['place'];
    $sub_array[] = $row['mobile1'];
    $sub_array[] = $row['email'];
    $sub_array[] = !empty($row['joining_date']) ? date('d-m-Y', strtotime($row['joining_date'])) : '';
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['team_name'];
    $sub_array[] = $office_type[$row['off_type']] ?? '';
    $sub_array[] = $row['designation'];
    $sub_array[] = $row['reporting_person'];
    $sub_array[] = $branch_admin_type[$row['branch_admin']] ?? '';
    $sub_array[] = (!empty($row['relieve_date']) && $row['relieve_date'] != '0000-00-00')
        ? date('d-m-Y', strtotime($row['relieve_date']))
        : '';
    $sub_array[] = $pf_available_type[$row['pf_available']] ?? '';
    $sub_array[] = $esi_available_type[$row['esi_available']] ?? '';
    $sub_array[] = $pt_available_type[$row['pt_available']] ?? '';
    $sub_array[] = $row['shift_name'];
    $sub_array[] = moneyFormatIndia($row['total_ctc']);
    $sub_array[] = moneyFormatIndia($row['annual_ctc']);
    $sub_array[] = $status_type[$row['status']] ?? '';

    $data[] = $sub_array;
}

/* Response */
$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
);

echo json_encode($output);