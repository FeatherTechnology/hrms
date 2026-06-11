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
$question      = $_POST['params']['question'] ?? '';

/* Datatable Columns */
$column = [
    'ssf.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'ft.feedback_title',
    'fqm.feedback_questions',
    'ssf.answer',
    'ssf.created_date',
];

/* Base Query */
$baseQuery = "
FROM staff_sch_feedback ssf
LEFT JOIN feedback_titles ft ON ft.id = ssf.feedback_titles_id	
LEFT JOIN users u ON u.id = ssf.insert_login_id	
LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = u.staff_name_id)
LEFT JOIN department_creation dc ON dc.id = oi.department
LEFT JOIN feedback_questions_mapping fqm ON fqm.id = ssf.feedback_ques_map_id
WHERE 1=1
";

$params = [];

/* Filters */
if (!empty($company_id)) {
    $baseQuery .= " AND ft.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

if (!empty($title)) {
    $baseQuery .= " AND ssf.feedback_titles_id = :title ";
    $params[':title'] = $title;
}

if (!empty($question)) {
    $baseQuery .= " AND ssf.feedback_ques_map_id = :question ";
    $params[':question'] = $question;
}

if (!empty($from_date) && !empty($to_date)) {
    $baseQuery .= " AND DATE(ssf.created_date) BETWEEN '$from_date' AND '$to_date'";
}

/* Search */
if (!empty($_POST['search'])) {

    $search = trim($_POST['search']);

    $baseQuery .= "
    AND (
        sc.staff_id LIKE :search
        OR sc.staff_name LIKE :search
        OR ft.feedback_name LIKE :search
        OR ssf.commants LIKE :search
    )";

    $params[':search'] = "%{$search}%";
}

/* Select Query */
$query = "
SELECT
    ssf.id,
    sc.staff_id,
    sc.staff_name,
    dc.department_name,
    ft.feedback_title,
    fqm.feedback_questions,
    ssf.answer,
    ssf.created_date
" . $baseQuery;

/* Filtered Count */
$stmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$stmt->execute($params);
$recordsFiltered = $stmt->fetchColumn();

/* Total Count */
$stmt = $pdo->query("SELECT COUNT(*) FROM staff_sch_feedback");
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
    $sub_array[] = $row['feedback_title'];
    $sub_array[] = $row['feedback_questions'];
    $sub_array[] = $row['answer'];
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
