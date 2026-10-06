<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$userQry = $pdo->prepare("SELECT id,report_access,user_type,director_name,staff_name_id FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$user = $userQry->fetch(PDO::FETCH_ASSOC);
$report_access = $user['report_access'] ?? 1;
$user_type = $user['user_type'] ?? '';
$director_id = $user['director_name'] ?? '';
$staff_id = $user['staff_name_id'] ?? '';

$from_date = $_POST['params']['from_date'] ?? '';
$to_date = $_POST['params']['to_date'] ?? '';
$company_id = $_POST['params']['company_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$status = $_POST['params']['status'] ?? '';
$search = trim($_POST['search']['value'] ?? '');

$staff_arr = [
    1 => 'Employer',
    2 => 'Employee'
];

$req_type = [
    1 => 'Leave',
    2 => 'Permission',
    3 => 'Week Off',
    4 => 'OT'
];

$column = [
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
    'r.purpose',
    'r.updated_date',
    'r.remarks',
    'r.status'
];

$params = [];
$allowedStaffIds = [];

if ($report_access == 2) {
    if ($user_type == 1 && !empty($director_id)) {
        $hierarchyStmt = $pdo->prepare("SELECT
            rpm.reporting_staff,
            rp.user_type,
            rp.director_id,
            rp.reporting_person
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp ON rp.id = rpm.reporting_person_id");

        $hierarchyStmt->execute();
        $hierarchyRows = $hierarchyStmt->fetchAll(PDO::FETCH_ASSOC);

        $directStaff = [];
        $staffChildren = [];

        foreach ($hierarchyRows as $row) {
            $childStaff = (int)$row['reporting_staff'];

            if ($childStaff <= 0) {
                continue;
            }

            if ((int)$row['user_type'] === 1 && (string)$row['director_id'] === (string)$director_id) {
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
    } elseif ($user_type == 2 && !empty($staff_id)) {
        $staffSql = "SELECT DISTINCT rpm.reporting_staff
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp ON rp.id = rpm.reporting_person_id
            WHERE rp.user_type = 2
            AND rp.reporting_person = :staff_id";

        $staffParams = [
            ':staff_id' => $staff_id
        ];

        if (!empty($company_id)) {
            $staffSql .= " AND rp.company_id = :access_company_id";
            $staffParams[':access_company_id'] = $company_id;
        }

        $staffStmt = $pdo->prepare($staffSql);
        $staffStmt->execute($staffParams);

        $allowedStaffIds = $staffStmt->fetchAll(PDO::FETCH_COLUMN);
        $allowedStaffIds = array_map('intval', $allowedStaffIds);
    }
}

$baseQuery = "
    FROM regularization r
    LEFT JOIN staff_creation sc ON r.staff_profile_id = sc.id
    LEFT JOIN company_creation cc ON sc.company_id = cc.id
    INNER JOIN (
        SELECT oi1.*
        FROM occupation_info oi1
        INNER JOIN (
            SELECT staff_profile_id,MAX(id) AS max_id
            FROM occupation_info
            GROUP BY staff_profile_id
        ) latest ON oi1.id = latest.max_id
    ) oc ON oc.staff_profile_id = sc.id
    LEFT JOIN branch_creation bc ON oc.branch_id = bc.id
    LEFT JOIN department_creation d ON oc.department = d.id
    LEFT JOIN team_name_creation ti ON oc.team = ti.id
    LEFT JOIN designation_creation des ON oc.designation = des.id
    LEFT JOIN leave_creation lc ON r.leave_type = lc.id
    LEFT JOIN users u ON u.id = r.updated_login_id
    LEFT JOIN staff_creation approver_sc ON approver_sc.id = u.staff_name_id AND u.user_type = 2
    LEFT JOIN director_creation dc ON dc.id = u.director_name AND u.user_type = 1
    WHERE 1 = 1";

if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {
    if (!empty($allowedStaffIds)) {
        $accessPlaceholders = [];

        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':access_staff_' . $index;
            $accessPlaceholders[] = $placeholder;
            $params[$placeholder] = (int)$staffId;
        }

        $baseQuery .= " AND r.staff_profile_id IN (" . implode(',', $accessPlaceholders) . ")";
    } else {
        $baseQuery .= " AND 1 = 0";
    }
}

if (!empty($from_date) && !empty($to_date)) {
    $baseQuery .= " AND DATE(r.from_date) <= :from_date_to AND DATE(r.to_date) >= :from_date_from";
    $params[':from_date_from'] = $from_date;
    $params[':from_date_to'] = $to_date;
}

if (!empty($company_id)) {
    $baseQuery .= " AND r.company_id = :company_id";
    $params[':company_id'] = $company_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND r.dep_id = :department_id";
    $params[':department_id'] = $department_id;
}

if ($status !== '') {
    $baseQuery .= " AND r.status = :status";
    $params[':status'] = $status;
}

if (!empty($search)) {
    $searchValue = '%' . $search . '%';

    $baseQuery .= " AND (
        sc.staff_id LIKE :search_staff_id
        OR sc.staff_name LIKE :search_staff_name
        OR cc.company_name LIKE :search_company
        OR bc.branch_name LIKE :search_branch
        OR d.department_name LIKE :search_department
        OR ti.team_name LIKE :search_team
        OR des.designation LIKE :search_designation
        OR r.req_date LIKE :search_req_date
        OR r.purpose LIKE :search_purpose
        OR r.remarks LIKE :search_remarks
    )";

    $params[':search_staff_id'] = $search . '%';
    $params[':search_staff_name'] = $searchValue;
    $params[':search_company'] = $searchValue;
    $params[':search_branch'] = $searchValue;
    $params[':search_department'] = $searchValue;
    $params[':search_team'] = $searchValue;
    $params[':search_designation'] = $searchValue;
    $params[':search_req_date'] = $searchValue;
    $params[':search_purpose'] = $searchValue;
    $params[':search_remarks'] = $searchValue;
}

$query = "SELECT
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
    r.total_min,
    r.purpose,
    r.remarks,
    r.status,
    r.updated_date,
    r.updated_login_id,
    CASE
        WHEN u.user_type = '1' THEN COALESCE(dc.director_name,'')
        WHEN u.user_type = '2' THEN COALESCE(approver_sc.staff_name,'')
        ELSE ''
    END AS approver_name,
    COALESCE(
        (
            SELECT CASE
                WHEN rp_pending.user_type = 1 THEN COALESCE(dc_pending.director_name,'')
                WHEN rp_pending.user_type = 2 THEN COALESCE(sc_pending.staff_name,'')
                ELSE ''
            END
            FROM reporting_person_mapping rpm_pending
            INNER JOIN reporting_person rp_pending ON rp_pending.id = rpm_pending.reporting_person_id
            LEFT JOIN staff_creation sc_pending ON sc_pending.id = rp_pending.reporting_person
            LEFT JOIN director_creation dc_pending ON dc_pending.id = rp_pending.director_id
            WHERE rpm_pending.reporting_staff = r.staff_profile_id
            LIMIT 1
        ),
        ''
    ) AS assigned_to
    " . $baseQuery;

if (isset($_POST['order']) && isset($_POST['order'][0])) {
    $orderIndex = (int)($_POST['order'][0]['column'] ?? 0);
    $orderColumn = $column[$orderIndex] ?? 'r.id';
    $orderDirection = strtolower($_POST['order'][0]['dir'] ?? '') === 'asc' ? 'ASC' : 'DESC';
    $query .= " ORDER BY {$orderColumn} {$orderDirection}";
} else {
    $query .= " ORDER BY r.id DESC";
}

$countQuery = "SELECT COUNT(*) " . $baseQuery;
$countStmt = $pdo->prepare($countQuery);

foreach ($params as $key => $value) {
    $countStmt->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}

$countStmt->execute();
$number_filter_row = (int)$countStmt->fetchColumn();

$totalQuery = "SELECT COUNT(*) FROM regularization r";
$totalParams = [];
$totalWhere = " WHERE 1 = 1";

if (!empty($from_date) && !empty($to_date)) {
    $totalWhere .= " AND DATE(r.from_date) <= :total_to_date AND DATE(r.to_date) >= :total_from_date";
    $totalParams[':total_from_date'] = $from_date;
    $totalParams[':total_to_date'] = $to_date;
}

if (!empty($company_id)) {
    $totalWhere .= " AND r.company_id = :total_company_id";
    $totalParams[':total_company_id'] = $company_id;
}

if (!empty($department_id)) {
    $totalWhere .= " AND r.dep_id = :total_department_id";
    $totalParams[':total_department_id'] = $department_id;
}

if ($status !== '') {
    $totalWhere .= " AND r.status = :total_status";
    $totalParams[':total_status'] = $status;
}

if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {
    if (!empty($allowedStaffIds)) {
        $totalAccessPlaceholders = [];

        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':total_access_staff_' . $index;
            $totalAccessPlaceholders[] = $placeholder;
            $totalParams[$placeholder] = (int)$staffId;
        }

        $totalWhere .= " AND r.staff_profile_id IN (" . implode(',', $totalAccessPlaceholders) . ")";
    } else {
        $totalWhere .= " AND 1 = 0";
    }
}

$totalQuery .= $totalWhere;

$totalStmt = $pdo->prepare($totalQuery);

foreach ($totalParams as $key => $value) {
    $totalStmt->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}

$totalStmt->execute();
$recordsTotal = (int)$totalStmt->fetchColumn();

$length = (int)($_POST['length'] ?? 10);
$start = (int)($_POST['start'] ?? 0);

if ($length != -1) {
    $query .= " LIMIT :start, :length";
}

$statement = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $statement->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}

if ($length != -1) {
    $statement->bindValue(':start', $start, PDO::PARAM_INT);
    $statement->bindValue(':length', $length, PDO::PARAM_INT);
}

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = $start + 1;

foreach ($result as $row) {
    $isTimeBased = in_array((int)$row['req_type'], [2,4], true);

    $req_from_date = '';
    if (!empty($row['from_date'])) {
        $req_from_date = date(
            $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
            strtotime($row['from_date'])
        );
    }

    $req_to_date = '';
    if (!empty($row['to_date'])) {
        $req_to_date = date(
            $isTimeBased ? 'd-m-Y h:i A' : 'd-m-Y',
            strtotime($row['to_date'])
        );
    }

    $requested_date = '';
    if (!empty($row['req_date'])) {
        $requested_date = date('d-m-Y',strtotime($row['req_date']));
    }

    $cancelled_date = '';
    if ((int)$row['status'] === 2 && !empty($row['updated_date'])) {
        $cancelled_date = date('d-m-Y',strtotime($row['updated_date']));
    }

    $requested_days = formatDuration($row['total_min']);

    $sub_array = [];
    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'] ?? '';
    $sub_array[] = $row['staff_name'] ?? '';
    $sub_array[] = $staff_arr[$row['staff_type']] ?? '';
    $sub_array[] = $row['company_name'] ?? '';
    $sub_array[] = $row['branch_name'] ?? '';
    $sub_array[] = $row['department_name'] ?? '';
    $sub_array[] = $row['designation'] ?? '';
    $sub_array[] = $req_type[$row['req_type']] ?? '';
    $sub_array[] = $row['leave_type'] ?? '';
    $sub_array[] = $requested_date;
    $sub_array[] = $req_from_date;
    $sub_array[] = $req_to_date;
    $sub_array[] = $requested_days;
    $sub_array[] = $row['purpose'] ?? '';

    if ((int)$row['status'] === 0) {
        $sub_array[] = $row['assigned_to'] ?? '';
        $sub_array[] = 'Pending';
    } elseif ((int)$row['status'] === 1) {
        $sub_array[] = $row['approver_name'] ?? '';
        $sub_array[] = $row['remarks'] ?? '';
        $sub_array[] = 'Approved';
    } elseif ((int)$row['status'] === 2) {
        $sub_array[] = $row['approver_name'] ?? '';
        $sub_array[] = $cancelled_date;
        $sub_array[] = $row['remarks'] ?? '';
        $sub_array[] = 'Cancelled';
    }

    $data[] = $sub_array;
}

$output = [
    'draw' => intval($_POST['draw'] ?? 0),
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $number_filter_row,
    'data' => $data
];

echo json_encode($output);

function formatDuration($minutes)
{
    if (empty($minutes)) {
        return '0 Minutes';
    }

    $minutes = (int)$minutes;
    $days = floor($minutes / 1440);
    $hours = floor(($minutes % 1440) / 60);
    $mins = $minutes % 60;

    return "{$days} Days {$hours} Hours {$mins} Minutes";
}
?>