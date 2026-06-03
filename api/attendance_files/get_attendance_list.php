<?php
// Fetch attendance regularization list based on company, branch, and selected date.
// Shows staff details, attendance entry time, updated by, and reason.
// Non-admin users can view only their reporting staff records.

include '../../ajaxconfig.php';
session_start();
$userid = $_SESSION['user_id'] ?? "";

/* ---------- Input ---------- */
$company_id = $_POST['company_id'] ?? '';
$branch_id  = $_POST['branch_id'] ?? '';
$att_date   = !empty($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : '';

$staff_type = [1 => 'Employer', 2 => 'Employee'];

/* ---------- Logged In User Details ---------- */ 
$userQry = $pdo->prepare(" SELECT sc.staff_type, sc.company_id FROM users u LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id WHERE u.id = ? "); 
$userQry->execute([$userid]);
$userData = $userQry->fetch(PDO::FETCH_ASSOC);
$login_staff_type = $userData['staff_type'] ?? '';

/* ---------- Column mapping ---------- */
$columns = [
    'sc.staff_id',
    'sc.staff_name',
    'cc.company_name',
    'bc.branch_name',
    'dc.department_name',
    'dsc.designation',
    'tc.team_name',
    'sc.staff_type',
    'a.entry_time',
    'u.user_name',
    'a.reason'
];

/* ---------- Base Query ---------- */
$baseQuery = "
    FROM staff_creation sc

    LEFT JOIN occupation_info oi 
        ON oi.id = (
            SELECT MAX(id) 
            FROM occupation_info 
            WHERE staff_profile_id = sc.id
        )

    LEFT JOIN attendance a 
        ON a.staff_profile_id = sc.id 
        AND DATE(a.entry_time) = :att_date

    LEFT JOIN users u 
        ON u.id = a.updated_by

    LEFT JOIN company_creation cc 
        ON cc.id = oi.company_id

    LEFT JOIN branch_creation bc 
        ON bc.id = oi.branch_id

    LEFT JOIN department_creation dc 
        ON dc.id = oi.department

    LEFT JOIN designation_creation dsc 
        ON dsc.id = oi.designation

    LEFT JOIN team_name_creation tc 
        ON tc.id = oi.team

    WHERE oi.company_id = :company_id
      AND oi.branch_id = :branch_id
";

/* ---------- Search ---------- */
$params = [
    ':company_id' => $company_id,
    ':branch_id'  => $branch_id,
    ':att_date'   => $att_date
];

// this condition only for the employee to check the reporting person attendance only
if ($login_staff_type != 1) {

    $baseQuery .= "
        AND oi.reporting_person = :userid
    ";

    $params[':userid'] = $userid;
}

// search
if (!empty($_POST['search']['value'])) {
    $search = '%' . $_POST['search']['value'] . '%';

    $baseQuery .= "
        AND (
            sc.staff_id LIKE :search
            OR sc.staff_name LIKE :search
            OR cc.company_name LIKE :search
            OR bc.branch_name LIKE :search
            OR dc.department_name LIKE :search
            OR dsc.designation LIKE :search
            OR tc.team_name LIKE :search
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
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM staff_creation sc
    LEFT JOIN occupation_info oi 
        ON oi.id = (
            SELECT MAX(id) 
            FROM occupation_info 
            WHERE staff_profile_id = sc.id
        )
    WHERE oi.company_id = :company_id
      AND oi.branch_id = :branch_id
");

$totalStmt->execute([
    ':company_id' => $company_id,
    ':branch_id'  => $branch_id
]);

$recordsTotal = (int)$totalStmt->fetchColumn();

/* ---------- FILTERED COUNT ---------- */
$countStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$countStmt->execute($params);
$recordsFiltered = (int)$countStmt->fetchColumn();

/* ---------- DATA QUERY ---------- */
$dataQuery = "
    SELECT 
        sc.id as stf_id,
        sc.staff_id,
        sc.staff_name,
        sc.staff_type,
        cc.company_name,
        bc.branch_name,
        dc.department_name,
        dsc.designation,
        tc.team_name,
        a.entry_time,
        a.reason,
        a.id as att_id,
        a.insert_login_id,

        CASE 
            WHEN a.entry_time IS NULL THEN ''
            WHEN a.updated_by = :userid THEN u.user_name 
            ELSE 'Self'
        END as updated_by

    $baseQuery
    $orderBy
    $limit
";

$dataStmt = $pdo->prepare($dataQuery);

/* Bind fixed params */
$dataStmt->bindValue(':company_id', $company_id);
$dataStmt->bindValue(':branch_id', $branch_id);
$dataStmt->bindValue(':att_date', $att_date);
$dataStmt->bindValue(':userid', $userid);

/* Bind search */
if (!empty($params[':search'])) {
    $dataStmt->bindValue(':search', $params[':search']);
}

/* Bind pagination */
if ($_POST['length'] != -1) {
    $dataStmt->bindValue(':start', (int)$_POST['start'], PDO::PARAM_INT);
    $dataStmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
}

$dataStmt->execute();
$result = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- RESPONSE BUILD ---------- */
$data = [];
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    $data[] = [
        $sno++,
        $row['staff_id'],
        $row['staff_name'],
        $row['company_name'],
        $row['branch_name'],
        $row['department_name'],
        $row['designation'],
        $row['team_name'],
        $staff_type[$row['staff_type']] ?? '',
        !empty($row['entry_time']) ? date('d-m-Y h:i A', strtotime($row['entry_time'])) : '',
        $row['updated_by'],
        $row['reason'],
        "<span class='icon-border_color edit_add'
            data-id='{$row['stf_id']}'
            data-att_id='{$row['att_id']}'></span>"
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