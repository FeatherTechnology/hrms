<?php
// Get regularization request list based on status and user access permissions.

include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'] ?? "";

/* ---------- Input ---------- */
$type = $_POST['type'] ?? '';

/* ---------- Logged In User Details ---------- */
$userStmt = $pdo->prepare("SELECT
        u.staff_name_id,
        u.user_type,
        u.director_company,
        u.approval_required,
        u.allowed_request_type,
        u.approved_request_type,
        sc.staff_type,
        sc.company_id,
        dc.designation_level
    FROM users u
    LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = u.staff_name_id
        )
    LEFT JOIN designation_creation dc ON dc.id = oi.designation
    WHERE u.id = ?
");

$userStmt->execute([$userid]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC);

$staff_type         = $userData['staff_type'] ?? '';
$company_id         = $userData['company_id'] ?? '';
$my_staff_id        = $userData['staff_name_id'] ?? 0;
$approval_required  = $userData['approval_required'] ?? '';
$allowed_request_type  = $userData['allowed_request_type'] ?? '';
$approved_request_type  = $userData['approved_request_type'] ?? '';
$my_level           = $userData['designation_level'] ?? 0;
$user_type          = $userData['user_type'] ?? 0;
$director_company   = $userData['director_company'] ?? '';

/* ---------- Mappings ---------- */
$Req_type = [1 => 'Leave', 2 => 'Permission', 3 => 'Week Off', 4 => 'OT'];
$reg_status = [0 => 'Pending', 1 => 'Approved', 2 => 'Cancel'];

/* ---------- Column map for ordering ---------- */
$columns = [
    'stfcr.staff_id',
    'stfcr.staff_name',
    'cc.company_name',
    'bc.branch_name',
    'depcr.department_name',
    'descr.designation',
    'tc.team_name',
    'reg.req_date',
    'reg.req_type',
    'reg.from_date',
    'reg.to_date',
    'reg.total_min',
    'reg.status'
];

/* ---------- Base Query ---------- */
$baseQuery = "
    FROM regularization reg

    LEFT JOIN staff_creation stfcr 
        ON stfcr.id = reg.staff_profile_id

    LEFT JOIN company_creation cc 
        ON cc.id = reg.company_id

    LEFT JOIN occupation_info oc 
        ON oc.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = reg.staff_profile_id
        )

    LEFT JOIN branch_creation bc 
        ON bc.id = reg.branch_id

    LEFT JOIN department_creation depcr 
        ON depcr.id = reg.dep_id

    LEFT JOIN designation_creation descr 
        ON descr.id = reg.des_id

    LEFT JOIN team_name_creation tc 
        ON tc.id = reg.team_id

   WHERE 1 = 1
";

/* ---------- Filter Date Range for Current month and last month records ---------- */
$baseQuery .= "
    AND (
        DATE(reg.from_date) >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
        OR
        DATE(reg.to_date) >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
    )
";
$params = [];

if ($type == 'Request') {

    $baseQuery .= " AND reg.staff_profile_id = :my_staff_id ";
    $params[':my_staff_id'] = $my_staff_id;
}

if ($type == 'Approval') {

    // Only pending requests for approval
    $baseQuery .= " AND reg.status = 0 ";

    if ($user_type == 2) {
        $baseQuery .= " AND descr.designation_level > :my_level ";
        $params[':my_level'] = $my_level;
    }

    $types = array_map('intval', explode(',', $approved_request_type));
    $baseQuery .= " AND reg.req_type IN (" . implode(',', $types) . ")";
}

if ($user_type == 1) {

    // Director - director_company contains comma separated company ids
    $companyIds = array_filter(array_map('intval', explode(',', $director_company)));

    if (!empty($companyIds)) {
        $placeholders = [];

        foreach ($companyIds as $k => $id) {
            $key = ":cmp$k";
            $placeholders[] = $key;
            $params[$key] = $id;
        }

        $baseQuery .= " AND stfcr.company_id IN (" . implode(',', $placeholders) . ")";
    }
} else if ($user_type == 2) {

    // Company Admin
    $baseQuery .= " AND stfcr.company_id = :company_id ";
    $params[':company_id'] = $company_id;
}

/* ---------- Search ---------- */
if (!empty($_POST['search']['value'])) {
    $search = '%' . $_POST['search']['value'] . '%';

    $baseQuery .= "
        AND (
            stfcr.staff_id LIKE :search
            OR stfcr.staff_name LIKE :search
            OR cc.company_name LIKE :search
            OR bc.branch_name LIKE :search
            OR depcr.department_name LIKE :search
            OR descr.designation LIKE :search
            OR tc.team_name LIKE :search
            OR reg.req_date LIKE :search
        )
    ";

    $params[':search'] = $search;
}

/* ---------- ORDER ---------- */
$orderBy = '';
if (isset($_POST['order'][0]['column'])) {
    $colIndex = (int) $_POST['order'][0]['column'];
    $dir = ($_POST['order'][0]['dir'] === 'desc') ? 'DESC' : 'ASC';

    if (isset($columns[$colIndex])) {
        $orderBy = " ORDER BY {$columns[$colIndex]} $dir ";
    }
}

/* ---------- LIMIT ---------- */
$limit = '';
if ($_POST['length'] != -1) {
    $limit = " LIMIT :start, :length ";
}

/* ---------- TOTAL COUNT ---------- */
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM regularization reg");
$totalStmt->execute();
$recordsTotal = (int)$totalStmt->fetchColumn();

/* ---------- FILTERED COUNT ---------- */
$countStmt = $pdo->prepare("SELECT COUNT(*) $baseQuery");
$countStmt->execute($params);
$recordsFiltered = (int)$countStmt->fetchColumn();

/* ---------- DATA QUERY ---------- */
$dataQuery = "
    SELECT 
        stfcr.staff_id,
        stfcr.staff_name,
        cc.company_name,
        bc.branch_name,
        depcr.department_name,
        descr.designation,
        tc.team_name,
        reg.*
    $baseQuery
    $orderBy
    $limit
";

$dataStmt = $pdo->prepare($dataQuery);

/* Bind params */
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}

/* Bind pagination */
if ($_POST['length'] != -1) {
    $dataStmt->bindValue(':start', (int)$_POST['start'], PDO::PARAM_INT);
    $dataStmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
}

$dataStmt->execute();
$result = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Response ---------- */
$data = [];
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    /* minutes to format */
    $minutes = $row['total_min'];

    $days = floor($minutes / (24 * 60));
    $hours = floor(($minutes % (24 * 60)) / 60);
    $mins = $minutes % 60;

    $duration =
        "<div style='display:flex; gap:15px; align-items:center; justify-content:center;'>
            <span><span style='color:#f26b35;'>$days</span> D</span>
            <span><span style='color:#f26b35;'>$hours</span> H</span>
            <span><span style='color:#f26b35;'>$mins</span> M</span>
        </div>";

    $statusBadge = '';

    switch ($row['status']) {
        case 0:
            $statusBadge = "
            <span style='
                background:#FFFF00;
                color:#856404;
                padding:6px 25px;
                border-radius:5px;
                font-weight:600;
                font-size:12px;
            '>Pending</span>";
            break;

        case 1:
            $statusBadge = "
            <span style='
                background:#D4EDDA;
                color:#155724;
                padding:6px 20px;
                border-radius:5px;
                font-weight:600;
                font-size:12px;
            '>Approved</span>";
            break;

        case 2:
            $statusBadge = "
            <span style='
                background:#F8D7DA;
                color:#721C24;
                padding:6px 27px;
                border-radius:5px;
                font-weight:600;
                font-size:12px;
            '>Cancel</span>";
            break;
    }

    /* action */
    if ($row['insert_login_id'] == $userid) {
        $action = "<span class='icon-delete delete_reg' data-id='{$row['id']}' data-status='{$row['status']} data-from-date='{$row['from_date']}'> </span>";
    } else {
        $action = "<span class='icon-border_color edit_reg' data-id='{$row['id']}' data-staff_id='{$row['insert_login_id']}' data-status='{$row['status']}'</span>";
    }

    $data[] = [
        $sno++,
        $row['staff_id'],
        $row['staff_name'],
        $row['company_name'],
        $row['branch_name'],
        $row['department_name'],
        $row['designation'],
        $row['team_name'],
        !empty($row['req_date']) ? date('d-m-Y H:i:s', strtotime($row['req_date'])) : '',
        $Req_type[$row['req_type']] ?? '',
        !empty($row['from_date']) ? date('d-m-Y H:i:s', strtotime($row['from_date'])) : '',
        !empty($row['to_date']) ? date('d-m-Y H:i:s', strtotime($row['to_date'])) : '',
        $duration,
        $statusBadge,
        $action
    ];
}

/* ---------- OUTPUT ---------- */
echo json_encode([
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
]);

$pdo = null;
