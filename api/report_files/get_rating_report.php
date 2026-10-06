<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? '';

$from_date     = $_POST['params']['from_date'] ?? '';
$to_date       = $_POST['params']['to_date'] ?? '';
$company_id    = $_POST['params']['company_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$title         = $_POST['params']['title'] ?? '';

$rating_type = [1 => 'Poor', 2 => 'Below Average', 3 => 'Average', 4 => 'Good', 5 => 'Excellent'];

$userQry = $pdo->prepare("SELECT user_type, staff_name_id, director_name, report_access FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$userRow = $userQry->fetch(PDO::FETCH_ASSOC);

$user_type     = (int)($userRow['user_type'] ?? 0);
$staff_name_id = (int)($userRow['staff_name_id'] ?? 0);
$director_name = (int)($userRow['director_name'] ?? 0);
$report_access = (int)($userRow['report_access'] ?? 1);

$column = [
    'ra.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'rt.rating_title',
    'ra.rating_value',
    'ra.reason',
    'ra.created_date'
];

$withQuery = '';
$reportCondition = '';
$reportParams = [];

if ($report_access == 2 && $user_type == 1) {
    $withQuery = "WITH RECURSIVE reporting_tree AS (
            SELECT DISTINCT
                CAST(rpm1.reporting_staff AS UNSIGNED) AS staff_id
            FROM reporting_person_mapping rpm1
            INNER JOIN reporting_person rp1
                ON rp1.id = rpm1.reporting_person_id
            WHERE rp1.user_type = 1
              AND rp1.director_id = :director_id
              AND rpm1.reporting_staff IS NOT NULL
              AND rpm1.reporting_staff != ''
            UNION
            SELECT DISTINCT
                CAST(rpm2.reporting_staff AS UNSIGNED) AS staff_id
            FROM reporting_person_mapping rpm2
            INNER JOIN reporting_person rp2
                ON rp2.id = rpm2.reporting_person_id
            INNER JOIN reporting_tree rt
                ON rt.staff_id = rp2.reporting_person
            WHERE rp2.user_type = 2
              AND rpm2.reporting_staff IS NOT NULL
              AND rpm2.reporting_staff != '')";
    $reportCondition = "AND EXISTS (
            SELECT 1 FROM reporting_tree rt WHERE rt.staff_id = sc.id)";
    $reportParams[':director_id'] = $director_name;

} elseif ($report_access == 2 && $user_type == 2) {
    $reportCondition = "AND EXISTS (
            SELECT 1
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp
                ON rp.id = rpm.reporting_person_id
            WHERE rpm.reporting_staff = sc.id
              AND rp.user_type = 2
              AND rp.reporting_person = :reporting_person
              AND rp.company_id = oi.company_id
        )
    ";

    $reportParams[':reporting_person'] = $staff_name_id;
}

$baseQuery = "FROM rating_answers ra
LEFT JOIN rating_titles rt
    ON rt.id = ra.rating_titles_id
LEFT JOIN users u
    ON u.id = ra.insert_login_id
LEFT JOIN staff_creation sc
    ON sc.id = u.staff_name_id
LEFT JOIN occupation_info oi
    ON oi.id = (
        SELECT MAX(oi2.id)
        FROM occupation_info oi2
        WHERE oi2.staff_profile_id = u.staff_name_id
        AND oi2.effective_from <= NOW()
    )
LEFT JOIN department_creation dc
    ON dc.id = oi.department
WHERE 1=1";

$params = $reportParams;

if (!empty($company_id)) {
    $baseQuery .= " AND rt.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND oi.department = :department_id ";
    $params[':department_id'] = $department_id;
}

if (!empty($title)) {
    $baseQuery .= " AND ra.rating_titles_id = :title ";
    $params[':title'] = $title;
}

if (!empty($from_date) && !empty($to_date)) {
    $baseQuery .= " AND DATE(ra.created_date) BETWEEN :from_date AND :to_date ";
    $params[':from_date'] = $from_date;
    $params[':to_date'] = $to_date;
}

$baseQuery .= $reportCondition;

if (!empty($_POST['search']['value'])) {
    $search = trim($_POST['search']['value']);
    $baseQuery .= "AND (
        sc.staff_id LIKE :search
        OR sc.staff_name LIKE :search
        OR rt.rating_title LIKE :search
        OR ra.rating_value LIKE :search
        OR ra.reason LIKE :search
    )";
    $params[':search'] = "%{$search}%";
}

$query = $withQuery . "SELECT
    ra.id,
    sc.staff_id,
    sc.staff_name,
    dc.department_name,
    rt.rating_title,
    ra.rating_value,
    ra.reason,
    ra.created_date
" . $baseQuery;

$countQuery = $withQuery . "SELECT COUNT(*)" . $baseQuery;
$stmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$recordsFiltered = $stmt->fetchColumn();

if ($report_access == 2) {
    $totalParams = $reportParams;

    $totalQuery = $withQuery . "SELECT COUNT(*)
    FROM rating_answers ra
    LEFT JOIN users u
        ON u.id = ra.insert_login_id
    LEFT JOIN staff_creation sc
        ON sc.id = u.staff_name_id
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(oi2.id)
            FROM occupation_info oi2
            WHERE oi2.staff_profile_id = u.staff_name_id
            AND oi2.effective_from <= NOW()
        )
    LEFT JOIN rating_titles rt
        ON rt.id = ra.rating_titles_id
    WHERE 1=1";

    if (!empty($company_id)) {
        $totalQuery .= " AND rt.company_id = :company_id ";
        $totalParams[':company_id'] = $company_id;
    }

    if (!empty($department_id)) {
        $totalQuery .= " AND oi.department = :department_id ";
        $totalParams[':department_id'] = $department_id;
    }

    $totalQuery .= $reportCondition;
    $stmt = $pdo->prepare($totalQuery);

    foreach ($totalParams as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $recordsTotal = $stmt->fetchColumn();

} else {
    $stmt = $pdo->query("SELECT COUNT(*) FROM rating_answers");
    $recordsTotal = $stmt->fetchColumn();
}

if (isset($_POST['order'])) {
    $orderIndex = (int)$_POST['order'][0]['column'];
    $orderColumn = $column[$orderIndex] ?? 'ra.id';
    $orderDir = ($_POST['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';
    $query .= " ORDER BY {$orderColumn} {$orderDir}";
} else {
    $query .= " ORDER BY ra.id DESC";
}

if ((int)($_POST['length'] ?? 10) != -1) {
    $query .= " LIMIT :start, :length";
}

$stmt = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

if ((int)($_POST['length'] ?? 10) != -1) {
    $stmt->bindValue(':start', (int)($_POST['start'] ?? 0), PDO::PARAM_INT);
    $stmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = (int)($_POST['start'] ?? 0) + 1;

foreach ($result as $row) {
    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['rating_title'];
    $sub_array[] = $rating_type[$row['rating_value']] ?? '';
    $sub_array[] = $row['reason'];
    $sub_array[] = !empty($row['created_date']) ? date('d-m-Y', strtotime($row['created_date'])) : '';

    $data[] = $sub_array;
}

$output = [
    "draw" => intval($_POST['draw'] ?? 0),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
];

echo json_encode($output);
