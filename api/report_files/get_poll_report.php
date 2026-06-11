<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? '';

/* Filters */
$from_date     = $_POST['params']['from_date'] ?? '';
$to_date       = $_POST['params']['to_date'] ?? '';
$company_id    = $_POST['params']['company_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$title         = $_POST['params']['title'] ?? '';

/* Datatable Columns */
$column = [
    'pa.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'pt.poll_title',
    'pom.poll_options',
    'pa.reason',
    'pa.created_date',
];

/* Base Query */
$baseQuery = "
FROM poll_answers pa
LEFT JOIN poll_titles pt ON pt.id = pa.poll_titles_id	
LEFT JOIN users u ON u.id = pa.insert_login_id	
LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = u.staff_name_id)
LEFT JOIN department_creation dc ON dc.id = oi.department
LEFT JOIN poll_options_mapping pom ON pom.id = pa.poll_value
WHERE 1=1
";

$params = [];

/* Filters */
if (!empty($company_id)) {
    $baseQuery .= " AND pt.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

if (!empty($title)) {
    $baseQuery .= " AND pa.poll_titles_id = :title ";
    $params[':title'] = $title;
}

if (!empty($from_date) && !empty($to_date)) {
    $baseQuery .= " AND DATE(pa.created_date) BETWEEN '$from_date' AND '$to_date'";
}

/* Search */
if (!empty($_POST['search'])) {

    $search = trim($_POST['search']);

    $baseQuery .= "
    AND (
        sc.staff_id LIKE :search
        OR sc.staff_name LIKE :search
        OR pt.poll_title LIKE :search
        OR pom.poll_options LIKE :search
        OR dc.department_name LIKE :search
        OR pa.reason LIKE :search
        OR pa.commants LIKE :search
    )";

    $params[':search'] = "%{$search}%";
}

/* Select Query */
$query = "
SELECT
    pa.id,
    sc.staff_id,
    sc.staff_name,
    dc.department_name,
    pt.poll_title,
    pom.poll_options,
    pa.reason,
    pa.created_date
" . $baseQuery;

/* Filtered Count */
$stmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$stmt->execute($params);
$recordsFiltered = $stmt->fetchColumn();

/* Total Count */
$stmt = $pdo->query("SELECT COUNT(*) FROM poll_answers");
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
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['poll_title'];
    $sub_array[] = $row['poll_options'];
    $sub_array[] = $row['reason'];
    $sub_array[] = !empty($row['created_date']) ? date('d-m-Y', strtotime($row['created_date'])) : '';

    $data[] = $sub_array;
}

/* Response */
$output = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
];

echo json_encode($output);
