<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? '';

$from_date    = $_POST['params']['from_date'] ?? '';
$to_date      = $_POST['params']['to_date'] ?? '';
$company_id   = $_POST['params']['company_id'] ?? '';
$branch_id    = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

/*--- Column Array ---*/
$column = array(
    'oi.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'des.designation',
    'bc.branch_name',
    'bcs.branch_name',
    'lam.lattitude_longitude',
    'lam.from_date',
    'lam.to_date',
    'lam.no_of_days',
    'u.user_name',
    'lam.reason'
);

/* --- Base Query --- */
$baseQuery = "
FROM occupation_info oi

LEFT JOIN branch_creation bc
    ON oi.branch_id = bc.id

LEFT JOIN department_creation dc
    ON oi.department = dc.id

LEFT JOIN staff_creation sc
    ON oi.staff_profile_id = sc.id

LEFT JOIN designation_creation des
    ON des.id = oi.designation

LEFT JOIN location_access_mapping lam
    ON lam.id = (
        SELECT id
        FROM location_access_mapping
        WHERE staff_profile_id = oi.staff_profile_id
        AND status = 0
        ORDER BY id DESC
        LIMIT 1
    )

LEFT JOIN branch_creation bcs
    ON lam.assigned_branch = bcs.id

LEFT JOIN users u
    ON u.id = :user_id

WHERE oi.off_type = 1
AND oi.id IN (
    SELECT MAX(id)
    FROM occupation_info
    GROUP BY staff_profile_id
)
AND oi.reporting_person = u.staff_name_id
AND lam.from_date <= :to_date
AND lam.to_date >= :from_date
";

$params = [
    ':user_id' => $user_id,
    ':from_date' => $from_date,
    ':to_date' => $to_date
];

/* --- Filters --- */

if (!empty($company_id)) {
    $baseQuery .= " AND oi.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

if (!empty($branch_id)) {
    $baseQuery .= " AND oi.branch_id = :branch_id ";
    $params[':branch_id'] = $branch_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND oi.department = :department_id ";
    $params[':department_id'] = $department_id;
}

/* --- Search --- */
if (!empty($_POST['search'])) {

    $search = trim($_POST['search']);

    $baseQuery .= "
    AND (
        sc.staff_id LIKE :search_staff_id
        OR sc.staff_name LIKE :search
        OR dc.department_name LIKE :search
        OR des.designation LIKE :search
        OR bc.branch_name LIKE :search
        OR bcs.branch_name LIKE :search
        OR lam.from_date LIKE :search
        OR lam.to_date LIKE :search
        OR lam.lattitude_longitude LIKE :search
        OR u.user_name LIKE :search
        OR lam.reason LIKE :search
    )";

    $params[':search_staff_id'] = $search . '%';
    $params[':search'] = '%' . $search . '%';
}

/* --- Main Select Query --- */
$query = "
SELECT
    oi.id,
    sc.staff_id,
    sc.staff_name,
    dc.department_name,
    des.designation,
    bc.branch_name,
    bcs.branch_name AS assigned_branch_name,
    lam.from_date,
    lam.to_date,
    lam.no_of_days,
    lam.reason,
    lam.lattitude_longitude,
    oi.staff_profile_id,
    u.user_name
" . $baseQuery . "
GROUP BY oi.id
";

/* --- Filtered Count --- */
$countQuery = "SELECT COUNT(*) FROM (SELECT oi.id " . $baseQuery . " GROUP BY oi.id) AS temp";

$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$recordsFiltered = $stmt->fetchColumn();

/* --- Total Count ---- */
$totalQuery = "SELECT COUNT(*) FROM occupation_info WHERE off_type = 1";

$stmt = $pdo->prepare($totalQuery);
$stmt->execute();
$recordsTotal = $stmt->fetchColumn();

/* --- Order --- */
if (isset($_POST['order'])) {

    $orderColumn = $column[$_POST['order'][0]['column']];
    $orderDir = ($_POST['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';

    $query .= " ORDER BY {$orderColumn} {$orderDir}";
} else {

    $query .= " ORDER BY sc.id DESC";
}

/* --- Pagination --- */
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

/* --- Data Formatting --- */
$data = [];
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['designation'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['assigned_branch_name'];
    $sub_array[] = $row['lattitude_longitude'];

    $sub_array[] = !empty($row['from_date'])
        ? date('d-m-Y', strtotime($row['from_date']))
        : '';

    $sub_array[] = !empty($row['to_date'])
        ? date('d-m-Y', strtotime($row['to_date']))
        : '';

    $sub_array[] = $row['no_of_days'];
    $sub_array[] = $row['user_name'];
    $sub_array[] = $row['reason'];

    $data[] = $sub_array;
}

/* --- Output --- */
$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
);

echo json_encode($output);
