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
$userStmt = $pdo->prepare("
    SELECT
        u.staff_name_id,
        u.user_type,
        u.director_name,
        u.director_company
    FROM users u
    WHERE u.id = ?
");

$userStmt->execute([$userid]);
$userData = $userStmt->fetch(PDO::FETCH_ASSOC);

$my_staff_id      = $userData['staff_name_id'] ?? 0;
$user_type        = $userData['user_type'] ?? 0;
$director_id      = $userData['director_name'] ?? 0;
$director_company = $userData['director_company'] ?? '';

/* ---------- Column mapping ---------- */
$columns = [
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'cc.company_name',
    'bc.branch_name',
    'dc.department_name',
    'dsc.designation',
    'tc.team_name',
    'sc.staff_type',
    'COALESCE(a.updated_time, a.entry_time)',
    'u.user_name',
    'COALESCE(a.updated_exit_time, a.exit_time)',
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
            WHERE staff_profile_id = sc.id AND effective_from <= NOW()
        )

    LEFT JOIN attendance a 
        ON a.staff_profile_id = sc.id 
        AND DATE(COALESCE(a.updated_time, a.entry_time)) = :att_date

    LEFT JOIN users u
    ON u.id = a.updated_by

    LEFT JOIN users eu
    ON eu.id = a.updated_exit_by

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

    WHERE oi.branch_id = :branch_id
     AND (
    sc.status = 1
    OR (
        sc.status = 2
        AND DATE(sc.relieve_date) >= :att_date
    )
)
";

/* ---------- Search ---------- */
$params = [
    ':branch_id' => $branch_id,
    ':att_date'  => $att_date
];


if ($user_type == 2) {

    $baseQuery .= "
        AND sc.id IN (
            SELECT rpm.reporting_staff
            FROM reporting_person rp

            INNER JOIN reporting_person_mapping rpm
                ON rpm.reporting_person_id = rp.id

            WHERE rp.user_type = 2
              AND rp.reporting_person = :my_staff_id
        )
    ";

    $params[':my_staff_id'] = $my_staff_id;

    $baseQuery .= "
        AND oi.company_id = :company_id
    ";

    $params[':company_id'] = $company_id;
}


if ($user_type == 1) {

    $allStaffIds = [];

    $directStmt = $pdo->prepare("
        SELECT DISTINCT rpm.reporting_staff

        FROM reporting_person rp

        INNER JOIN reporting_person_mapping rpm
            ON rpm.reporting_person_id = rp.id

        WHERE rp.user_type = 1
          AND rp.director_id = ?
    ");

    $directStmt->execute([$director_id]);

    $directStaffIds = $directStmt->fetchAll(PDO::FETCH_COLUMN);

    $directStaffIds = array_values(
        array_filter(
            array_map('intval', $directStaffIds)
        )
    );

    $allStaffIds = $directStaffIds;

    $currentLevelIds = $directStaffIds;

    $processedIds = [];


    while (!empty($currentLevelIds)) {

        $currentLevelIds = array_values(
            array_diff(
                $currentLevelIds,
                $processedIds
            )
        );

        if (empty($currentLevelIds)) {
            break;
        }

        foreach ($currentLevelIds as $staffId) {
            $processedIds[] = $staffId;
        }
        $placeholders = [];
        $childParams = [];

        foreach ($currentLevelIds as $key => $staffId) {

            $placeholder = ":parent_$key";

            $placeholders[] = $placeholder;

            $childParams[$placeholder] = $staffId;
        }
        $childSql = "
            SELECT DISTINCT rpm.reporting_staff

            FROM reporting_person rp

            INNER JOIN reporting_person_mapping rpm
                ON rpm.reporting_person_id = rp.id

            WHERE rp.user_type = 2

              AND rp.reporting_person IN (
                  " . implode(',', $placeholders) . "
              )
        ";

        $childStmt = $pdo->prepare($childSql);


        foreach ($childParams as $key => $value) {

            $childStmt->bindValue(
                $key,
                $value,
                PDO::PARAM_INT
            );
        }

        $childStmt->execute();
        $childIds = $childStmt->fetchAll(PDO::FETCH_COLUMN);
        $childIds = array_values(
            array_filter(
                array_map('intval', $childIds)
            )
        );

        $childIds = array_values(
            array_diff(
                $childIds,
                $allStaffIds
            )
        );
        if (!empty($childIds)) {

            $allStaffIds = array_merge(
                $allStaffIds,
                $childIds
            );
        }
        $currentLevelIds = $childIds;
    }


    if (!empty($allStaffIds)) {

        $staffPlaceholders = [];

        foreach ($allStaffIds as $key => $staffId) {

            $placeholder = ":report_staff_$key";

            $staffPlaceholders[] = $placeholder;

            $params[$placeholder] = $staffId;
        }


        $baseQuery .= "
            AND sc.id IN (
                " . implode(',', $staffPlaceholders) . "
            )
        ";

    } else {

        $baseQuery .= "
            AND 1 = 0
        ";
    }

    $companyIds = array_filter(
        array_map(
            'intval',
            explode(',', $director_company)
        )
    );


    if (!empty($companyIds)) {

        $companyPlaceholders = [];

        foreach ($companyIds as $key => $companyId) {

            $placeholder = ":director_company_$key";

            $companyPlaceholders[] = $placeholder;

            $params[$placeholder] = $companyId;
        }

        $baseQuery .= "
            AND oi.company_id IN (
                " . implode(',', $companyPlaceholders) . "
            )
        ";
    }

    if ($company_id != '') {

        $baseQuery .= "
            AND oi.company_id = :selected_company_id
        ";

        $params[':selected_company_id'] = $company_id;
    }
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
      AND sc.status = 1
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
        a.updated_time,
        a.exit_time,
        a.updated_exit_time,
        a.reason,
        a.id as att_id,
        a.insert_login_id,
        oi.shift,
        u.user_name AS updated_by,
        eu.user_name AS updated_exit_by

    $baseQuery
    $orderBy
    $limit
";

$dataStmt = $pdo->prepare($dataQuery);

foreach ($params as $key => $value) {
    $dataStmt->bindValue($key, $value);
}


/* Bind pagination */
if ($_POST['length'] != -1) {
    $dataStmt->bindValue(':start', (int)$_POST['start'], PDO::PARAM_INT);
    $dataStmt->bindValue(':length', (int)$_POST['length'], PDO::PARAM_INT);
}

$dataStmt->execute();
$result = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- MONTH RESTRICTION ---------- */
$currentMonth  = date('Y-m');
$selectedMonth  = date('Y-m', strtotime($att_date));

/* ---------- RESPONSE ---------- */
$data = [];
$sno = $_POST['start'] + 1;

foreach ($result as $row) {

    if ($selectedMonth == $currentMonth ) {
        $editBtn = "<span class='icon-border_color edit_add'
                        data-id='{$row['stf_id']}'
                        data-att_id='{$row['att_id']}'></span>";
    } else {
        $editBtn = "<span class='icon-border_color text-secondary'
                        style='opacity:0.5; cursor:not-allowed;'></span>";
    }

    $chartBtn = "
    <button type='button'
        class='btn btn-sm btn-info attendance_chart'
        data-company='{$row['company_name']}'
        data-company_id='{$company_id}'
        data-shift='{$row['shift']}'
        data-staff_id='{$row['stf_id']}'
        data-staff_name=\"{$row['staff_name']}\"
        data-date='{$att_date}'>
        Attendance Chart
    </button>";

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
        !empty($row['updated_time'])  ? date('d-m-Y h:i A', strtotime($row['updated_time']))  : (!empty($row['entry_time']) ? date('d-m-Y h:i A', strtotime($row['entry_time'])) : ''),
        $row['updated_by'],

       !empty($row['updated_exit_time']) && $row['updated_exit_time'] !== '0000-00-00 00:00:00'  ? date('d-m-Y h:i A',
        strtotime($row['updated_exit_time'])): ( !empty($row['exit_time']) &&  $row['exit_time'] !== '0000-00-00 00:00:00'  ? date('d-m-Y h:i A', 
        strtotime($row['exit_time']))  : '' ),

        $row['updated_exit_by'],
        $row['reason'],
        $chartBtn, // Attendance Chart
        $editBtn
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
