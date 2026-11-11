
<?php
include '../../ajaxconfig.php';

$company_id = (int)($_POST['company_id'] ?? 0);
$branch_id = $_POST['branch_id'] ?? [];
$dept_id = $_POST['dept_id'] ?? [];

if (!is_array($branch_id)) {
    $branch_id = ($branch_id !== '') ? [$branch_id] : [];
}

if (!is_array($dept_id)) {
    $dept_id = ($dept_id !== '') ? [$dept_id] : [];
}

$branch_id = array_filter(array_map('intval', $branch_id));
$dept_id = array_filter(array_map('intval', $dept_id));

if (!$company_id) {
    echo json_encode([]);
    exit;
}

$params = [':company_id' => $company_id];

$where = " WHERE sc.company_id = :company_id
           AND sc.status != 4
           AND (
                sc.relieve_date IS NULL
                OR sc.relieve_date = ''
                OR sc.relieve_date >= CURDATE()
           ) ";

$filterConditions = [];

// Branch filter
if (!empty($branch_id)) {
    $branchPlaceholders = [];

    foreach ($branch_id as $index => $id) {
        $key = ':branch' . $index;
        $branchPlaceholders[] = $key;
        $params[$key] = $id;
    }

    $filterConditions[] = "oi.branch_id IN (" . implode(',', $branchPlaceholders) . ")";
}

// Department filter
if (!empty($dept_id)) {
    $deptPlaceholders = [];

    foreach ($dept_id as $index => $id) {
        $key = ':dept' . $index;
        $deptPlaceholders[] = $key;
        $params[$key] = $id;
    }

    $filterConditions[] = "oi.department IN (" . implode(',', $deptPlaceholders) . ")";
}

// Either Branch OR Department
if (!empty($filterConditions)) {
    $where .= " AND (" . implode(' AND ', $filterConditions) . ") ";
}

$sql = "
    SELECT
        sc.id,
        sc.staff_name
    FROM staff_creation sc
    INNER JOIN (
        SELECT oi1.*
        FROM occupation_info oi1
        INNER JOIN (
            SELECT staff_profile_id, MAX(id) AS latest_id
            FROM occupation_info
            GROUP BY staff_profile_id
        ) latest ON latest.latest_id = oi1.id
    ) oi ON sc.id = oi.staff_profile_id
    $where
    ORDER BY sc.staff_name ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
