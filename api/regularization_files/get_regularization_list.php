<?php
// Get regularization request list based on status and user access permissions.

include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'] ?? "";

/* ---------- Input ---------- */
$status = $_POST['sts'] ?? '';

/* ---------- Logged In User Details ---------- */
$userQry = $pdo->prepare("
    SELECT sc.staff_type, sc.company_id
    FROM users u
    LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
    WHERE u.id = ?
");
$userQry->execute([$userid]);
$userData = $userQry->fetch(PDO::FETCH_ASSOC);

$staff_type = $userData['staff_type'] ?? '';
$company_id = $userData['company_id'] ?? '';


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
            WHERE staff_id = reg.staff_profile_id
        )

    LEFT JOIN branch_creation bc 
        ON bc.id = reg.branch_id

    LEFT JOIN department_creation depcr 
        ON depcr.id = reg.dep_id

    LEFT JOIN designation_creation descr 
        ON descr.id = reg.des_id

    LEFT JOIN team_name_creation tc 
        ON tc.id = reg.team_id

   WHERE reg.status = :status
";

/* ---------- Params ---------- */
$params = [
    ':status' => $status
];

if ($staff_type == 1) {

    // Employer/Admin - show all records from same company
    $baseQuery .= " AND stfcr.company_id = :company_id ";
    $params[':company_id'] = $company_id;

} else {

    // Existing condition unchanged
    $baseQuery .= "
        AND (
            oc.reporting_person = :userid
            OR reg.insert_login_id = :userid
        )
    ";

    $params[':userid'] = $userid;
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

    $duration = $days . " Days " . $hours . " Hours " . $mins . " Minutes";

    /* action */
    if ($row['insert_login_id'] == $userid) {
         $action = "<span class='icon-delete delete_reg' data-id='" . $row['id'] . "' data-status = '".$row['status']."' data-appFrom = '".$row['approved_from_date']."' data-appTo = '".$row['approved_to_date']."'></span>";
                    
    } else {
        $action = "<span class='icon-border_color edit_reg' data-id='{$row['id']}' data-staff_id='{$row['insert_login_id']}'data-status='{$row['status']}' data-appFrom='{$row['approved_from_date']}' data-appTo='{$row['approved_to_date']}'></span>";    }

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
        $reg_status[$row['status']] ?? '',
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