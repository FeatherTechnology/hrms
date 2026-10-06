<?php
require "../../ajaxconfig.php";
require "../../moneyFormatIndia.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? 0;

$userQry = $pdo->prepare("SELECT id, report_access, user_type, director_name, staff_name_id FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$user = $userQry->fetch(PDO::FETCH_ASSOC);

$report_access = $user['report_access'] ?? 1;
$user_type = $user['user_type'] ?? '';
$director_id = $user['director_name'] ?? '';
$staff_id = $user['staff_name_id'] ?? '';

$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$search = trim($_POST['search'] ?? '');

if (empty($from_date)) {
    $from_date = date('Y-m-01');
}

if (empty($to_date)) {
    $to_date = date('Y-m-t');
}

$params = [
    ':from_date' => $from_date,
    ':to_date' => $to_date
];

$column = [
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
    'assigned_by',
    'lam.reason'
];

$allowedStaffIds = [];
if ($report_access == 2) {
    /* Director Access */
    if ($user_type == 1 && !empty($director_id)) {
        $hierarchyStmt = $pdo->prepare("
            SELECT rpm.reporting_staff, rp.user_type, rp.director_id, rp.reporting_person
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp ON rp.id = rpm.reporting_person_id
        ");

        $hierarchyStmt->execute();
        $hierarchyRows = $hierarchyStmt->fetchAll(PDO::FETCH_ASSOC);
        $directStaff = [];
        $staffChildren = [];

        foreach ($hierarchyRows as $row) {
            $childStaff = (int)$row['reporting_staff'];
            if ($childStaff <= 0) {
                continue;
            }

            if ((int)$row['user_type'] === 1 &&(string)$row['director_id'] === (string)$director_id) {
                $directStaff[] = $childStaff;
            }
        
            if ((int)$row['user_type'] === 2 && !empty($row['reporting_person'])) {
                $parentStaff = (int)$row['reporting_person'];
                if (!isset($staffChildren[$parentStaff])) {
                    $staffChildren[$parentStaff] = [];
                }
                $staffChildren[$parentStaff][] = $childStaff;
            }
        }

        $directStaff = array_unique($directStaff);
        $queue = array_values($directStaff);
        while (!empty($queue)) {
            $currentStaff = array_shift($queue);
            if (in_array($currentStaff, $allowedStaffIds, true)) {
                continue;
            }
            $allowedStaffIds[] = $currentStaff;
            if (!empty($staffChildren[$currentStaff])) {
                foreach ($staffChildren[$currentStaff] as $childStaff) {
                    if (!in_array($childStaff, $allowedStaffIds, true)) {
                        $queue[] = $childStaff;
                    }
                }
            }
        }
    }

    /* Staff Access */
    elseif ($user_type == 2 && !empty($staff_id)) {
        $staffSql = "
            SELECT DISTINCT rpm.reporting_staff
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp ON rp.id = rpm.reporting_person_id
            WHERE rp.user_type = 2
            AND rp.reporting_person = :staff_id";
        if (!empty($company_id)) {
            $staffSql .= " AND rp.company_id = :access_company_id";
        }
        $staffStmt = $pdo->prepare($staffSql);
        $staffParams = [':staff_id' => $staff_id];
        if (!empty($company_id)) {
            $staffParams[':access_company_id'] = $company_id;
        }
        $staffStmt->execute($staffParams);
        $allowedStaffIds = $staffStmt->fetchAll(PDO::FETCH_COLUMN);
        $allowedStaffIds = array_map('intval', $allowedStaffIds);
    }
}

/* Access placeholders */

$accessCondition = '';
if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {
    if (!empty($allowedStaffIds)) {
        $accessPlaceholders = [];
        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':access_staff_' . $index;
            $accessPlaceholders[] = $placeholder;
            $params[$placeholder] = $staffId;
        }
        $accessCondition = implode(',', $accessPlaceholders);
    }
}

/* Base Query */

$baseQuery = "
FROM occupation_info oi
LEFT JOIN company_creation cc ON cc.id = oi.company_id
LEFT JOIN branch_creation bc ON bc.id = oi.branch_id
LEFT JOIN department_creation dc ON dc.id = oi.department
LEFT JOIN staff_creation sc ON sc.id = oi.staff_profile_id
LEFT JOIN designation_creation des ON des.id = oi.designation
LEFT JOIN location_access_mapping lam ON lam.staff_profile_id = oi.staff_profile_id AND lam.status = 0
LEFT JOIN branch_creation bcs ON bcs.id = lam.assigned_branch
LEFT JOIN users au ON au.id = lam.insert_login_id
LEFT JOIN staff_creation ascf ON ascf.id = au.staff_name_id AND au.user_type = 2
LEFT JOIN director_creation adir ON adir.id = au.director_name AND au.user_type = 1
WHERE oi.off_type = 1
AND oi.id IN (
    SELECT MAX(oi_latest.id)
    FROM occupation_info oi_latest
    WHERE oi_latest.effective_from <= NOW()
    GROUP BY oi_latest.staff_profile_id
)
AND lam.from_date <= :to_date
AND lam.to_date >= :from_date
";

/* Hierarchy condition */

if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {
    if (!empty($accessCondition)) {
        $baseQuery .= " AND oi.staff_profile_id IN ({$accessCondition})";
    } else {
        $baseQuery .= " AND 1 = 0";
    }
}

/* Filters */

if (!empty($company_id)) {
    $baseQuery .= " AND oi.company_id = :company_id";
    $params[':company_id'] = $company_id;
}

if (!empty($branch_id)) {
    $baseQuery .= " AND oi.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND oi.department = :department_id";
    $params[':department_id'] = $department_id;
}

/* Search */

if (!empty($search)) {

    $searchValue = '%' . $search . '%';

    $baseQuery .= "
        AND (
            sc.staff_id LIKE :search_staff_id
            OR sc.staff_name LIKE :search_staff_name
            OR dc.department_name LIKE :search_department
            OR des.designation LIKE :search_designation
            OR bc.branch_name LIKE :search_branch
            OR bcs.branch_name LIKE :search_assigned_branch
            OR lam.from_date LIKE :search_from_date
            OR lam.to_date LIKE :search_to_date
            OR lam.lattitude_longitude LIKE :search_location
            OR au.user_name LIKE :search_user
            OR lam.reason LIKE :search_reason
        )
    ";

    $params[':search_staff_id'] = $search . '%';
    $params[':search_staff_name'] = $searchValue;
    $params[':search_department'] = $searchValue;
    $params[':search_designation'] = $searchValue;
    $params[':search_branch'] = $searchValue;
    $params[':search_assigned_branch'] = $searchValue;
    $params[':search_from_date'] = $searchValue;
    $params[':search_to_date'] = $searchValue;
    $params[':search_location'] = $searchValue;
    $params[':search_user'] = $searchValue;
    $params[':search_reason'] = $searchValue;
}

/* Main Query */

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
    CASE
        WHEN au.user_type = 1 THEN COALESCE(adir.director_name, '')
        WHEN au.user_type = 2 THEN COALESCE(ascf.staff_name, '')
        ELSE COALESCE(au.user_name, '')
    END AS assigned_by
" . $baseQuery;

/* Filtered Count */

$countQuery = "SELECT COUNT(*) " . $baseQuery;
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$recordsFiltered = (int)$countStmt->fetchColumn();

/* Total Count */

$totalQuery = "
SELECT COUNT(*)
FROM occupation_info oi
WHERE oi.off_type = 1
AND oi.id IN (
    SELECT MAX(oi_latest.id)
    FROM occupation_info oi_latest
    WHERE oi_latest.effective_from <= NOW()
    GROUP BY oi_latest.staff_profile_id
)
";

$totalParams = [];

/* Total company filter */
if (!empty($company_id)) {
    $totalQuery .= " AND oi.company_id = :total_company_id";
    $totalParams[':total_company_id'] = $company_id;
}

/* Total branch filter */
if (!empty($branch_id)) {
    $totalQuery .= " AND oi.branch_id = :total_branch_id";
    $totalParams[':total_branch_id'] = $branch_id;
}

/* Total department filter */
if (!empty($department_id)) {
    $totalQuery .= " AND oi.department = :total_department_id";
    $totalParams[':total_department_id'] = $department_id;
}

/* Total hierarchy filter */
if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {
    if (!empty($allowedStaffIds)) {
        $totalAccessPlaceholders = [];
        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':total_access_staff_' . $index;
            $totalAccessPlaceholders[] = $placeholder;
            $totalParams[$placeholder] = $staffId;
        }
        $totalQuery .= " AND oi.staff_profile_id IN (" . implode(',', $totalAccessPlaceholders) . ")";
    } else {
        $totalQuery .= " AND 1 = 0";
    }
}

$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute($totalParams);
$recordsTotal = (int)$totalStmt->fetchColumn();

/* Ordering */

if (isset($_POST['order']) && isset($_POST['order'][0])) {
    $orderIndex = (int)($_POST['order'][0]['column'] ?? 0);
    $orderColumn = $column[$orderIndex] ?? 'oi.id';
    $orderDirection = strtolower($_POST['order'][0]['dir'] ?? '') === 'asc'? 'ASC': 'DESC';
    $query .= " ORDER BY {$orderColumn} {$orderDirection}";
} else {
    $query .= " ORDER BY oi.id DESC";
}

/* Pagination */
$length = (int)($_POST['length'] ?? 10);
$start = (int)($_POST['start'] ?? 0);
if ($length != -1) {
    $query .= " LIMIT :start, :length";
}

/* Execute Main Query */
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    if (is_int($value)) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
}

if ($length != -1) {
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = $start + 1;
foreach ($result as $row) {

    $fromDate = !empty($row['from_date']) ? date('d-m-Y', strtotime($row['from_date'])) : '';
    $toDate = !empty($row['to_date']) ? date('d-m-Y', strtotime($row['to_date'])) : '';
    $data[] = [
        $sno,
        $row['staff_id'] ?? '',
        $row['staff_name'] ?? '',
        $row['department_name'] ?? '',
        $row['designation'] ?? '',
        $row['branch_name'] ?? '',
        $row['assigned_branch_name'] ?? '',
        $row['lattitude_longitude'] ?? '',
        $fromDate,
        $toDate,
        $row['no_of_days'] ?? '',
        $row['assigned_by'] ?? '',
        $row['reason'] ?? ''
    ];
    $sno++;
}

echo json_encode([
    'draw' => intval($_POST['draw'] ?? 0),
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
]);
?>