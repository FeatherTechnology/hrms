<?php
require "../../ajaxconfig.php";
require "../../moneyFormatIndia.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? 0;

$userQry = $pdo->prepare("SELECT id, user_type, director_name, staff_name_id, staff_id, report_access FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$rowuser = $userQry->fetch(PDO::FETCH_ASSOC);

$report_access = $rowuser['report_access'] ?? 1;
$user_type = $rowuser['user_type'] ?? '';
$director_id = $rowuser['director_name'] ?? '';
$staff_id = $rowuser['staff_id'] ?? '';
$staff_name_id = $rowuser['staff_name_id'] ?? '';

$logged_staff_profile_id = '';
if ($user_type == 2) {
    $logged_staff_profile_id = $staff_name_id;
}

$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

$staff_type = [1 => 'Employer', 2 => 'Employee'];
$gender_type = [1 => 'Male', 2 => 'Female'];
$office_type = [1 => 'Office', 2 => 'Field'];
$pf_available_type = [1 => 'Yes', 2 => 'No'];
$esi_available_type = [1 => 'Yes', 2 => 'No'];
$pt_available_type = [1 => 'Yes', 2 => 'No'];
$status_type = [1 => 'Active', 2 => 'In Active'];

$column = array(
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.staff_type',
    'sc.gender',
    'sc.place',
    'sc.mobile1',
    'sc.email',
    'sc.joining_date',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'ti.team_name',
    'oc.off_type',
    'des.designation',
    'reporting_person',
    'sc.relieve_date',
    'sc.pf_available',
    'sc.esi_available',
    'sc.pt_available',
    'shc.shift_name',
    'oc.total_ctc',
    'oc.annual_ctc',
    'sc.status'
);

$baseQuery = "
FROM staff_creation sc
LEFT JOIN company_creation cc ON sc.company_id = cc.id
INNER JOIN (
    SELECT oi.*
    FROM occupation_info oi
    INNER JOIN (
        SELECT
            staff_profile_id,
            MAX(id) AS max_id
        FROM occupation_info
        WHERE effective_from <= NOW()
        GROUP BY staff_profile_id
    ) latest ON oi.id = latest.max_id
) oc ON oc.staff_profile_id = sc.id
LEFT JOIN reporting_person_mapping rpm
    ON rpm.id = (
        SELECT MIN(rpm2.id)
        FROM reporting_person_mapping rpm2
        INNER JOIN reporting_person rp2 ON rp2.id = rpm2.reporting_person_id
        WHERE rpm2.reporting_staff = sc.id
        AND rp2.company_id = sc.company_id
    )
LEFT JOIN reporting_person rp ON rp.id = rpm.reporting_person_id
LEFT JOIN staff_creation reporting_staff ON reporting_staff.id = rp.reporting_person AND rp.user_type = 2
LEFT JOIN director_creation dc ON dc.id = rp.director_id AND rp.user_type = 1
LEFT JOIN branch_creation bc ON bc.id = oc.branch_id
LEFT JOIN department_creation d ON d.id = oc.department
LEFT JOIN team_name_creation ti ON ti.id = oc.team
LEFT JOIN designation_creation des ON des.id = oc.designation
LEFT JOIN shift_creation shc ON shc.id = oc.shift
WHERE 1=1
";

$params = [];

if ($report_access == '2') {
    if ($user_type == '2' && !empty($logged_staff_profile_id)) {
        $baseQuery .= " AND sc.id IN (
            SELECT rpm_child.reporting_staff
            FROM reporting_person_mapping rpm_child
            INNER JOIN reporting_person rp_child ON rp_child.id = rpm_child.reporting_person_id
            WHERE rp_child.user_type = 2
            AND rp_child.reporting_person = :logged_staff_id
        )";
        $params[':logged_staff_id'] = $logged_staff_profile_id;
    } elseif ($user_type == '1' && !empty($director_id)) {
        $baseQuery .= " AND sc.id IN (
            WITH RECURSIVE reporting_tree AS (
                SELECT rpm1.reporting_staff AS staff_id
                FROM reporting_person_mapping rpm1
                INNER JOIN reporting_person rp1 ON rp1.id = rpm1.reporting_person_id
                WHERE rp1.user_type = 1
                AND rp1.director_id = :director_id

                UNION ALL

                SELECT rpm2.reporting_staff AS staff_id
                FROM reporting_person_mapping rpm2
                INNER JOIN reporting_person rp2 ON rp2.id = rpm2.reporting_person_id
                INNER JOIN reporting_tree rt ON rt.staff_id = rp2.reporting_person
                WHERE rp2.user_type = 2
            )
            SELECT staff_id FROM reporting_tree
        )";
        $params[':director_id'] = $director_id;
    }
}

if (!empty($company_id)) {
    $baseQuery .= " AND sc.company_id = :company_id";
    $params[':company_id'] = $company_id;
}

if (!empty($branch_id)) {
    $baseQuery .= " AND oc.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

if (!empty($department_id)) {
    $baseQuery .= " AND oc.department = :department_id";
    $params[':department_id'] = $department_id;
}

$search = '';

if (isset($_POST['search'])) {
    if (is_array($_POST['search'])) {
        $search = trim($_POST['search']['value'] ?? '');
    } else {
        $search = trim($_POST['search']);
    }
}

if ($search != '') {
    $baseQuery .= "
        AND (
            sc.staff_id LIKE :search_staff_id
            OR sc.staff_name LIKE :search_staff_name
            OR sc.mobile1 LIKE :search_mobile1
            OR sc.email LIKE :search_email
            OR sc.place LIKE :search_place
            OR cc.company_name LIKE :search_company
            OR bc.branch_name LIKE :search_branch
            OR d.department_name LIKE :search_department
            OR ti.team_name LIKE :search_team
            OR des.designation LIKE :search_designation
            OR reporting_staff.staff_name LIKE :search_reporting_staff
            OR dc.director_name LIKE :search_director
            OR shc.shift_name LIKE :search_shift
        )
    ";

    $searchValue = "%{$search}%";

    $params[':search_staff_id'] = $searchValue;
    $params[':search_staff_name'] = $searchValue;
    $params[':search_mobile1'] = $searchValue;
    $params[':search_email'] = $searchValue;
    $params[':search_place'] = $searchValue;
    $params[':search_company'] = $searchValue;
    $params[':search_branch'] = $searchValue;
    $params[':search_department'] = $searchValue;
    $params[':search_team'] = $searchValue;
    $params[':search_designation'] = $searchValue;
    $params[':search_reporting_staff'] = $searchValue;
    $params[':search_director'] = $searchValue;
    $params[':search_shift'] = $searchValue;
}

$query = "
SELECT
    sc.id,
    sc.staff_id,
    sc.staff_name,
    sc.staff_type,
    sc.gender,
    sc.place,
    sc.mobile1,
    sc.email,
    sc.joining_date,
    cc.company_name,
    bc.branch_name,
    d.department_name,
    ti.team_name,
    oc.off_type,
    des.designation,
    CASE
        WHEN rp.user_type = 2 THEN COALESCE(reporting_staff.staff_name, '')
        WHEN rp.user_type = 1 THEN COALESCE(dc.director_name, '')
        ELSE ''
    END AS reporting_person,
    sc.relieve_date,
    sc.pf_available,
    sc.esi_available,
    sc.pt_available,
    shc.shift_name,
    oc.total_ctc,
    oc.annual_ctc,
    sc.status
" . $baseQuery;

$stmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);

foreach ($params as $key => $value) {
    $stmt->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}

$stmt->execute();
$recordsFiltered = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM staff_creation");
$recordsTotal = (int)$stmt->fetchColumn();

if (isset($_POST['order'][0])) {
    $orderIndex = (int)($_POST['order'][0]['column'] ?? 0);
    $orderColumn = $column[$orderIndex] ?? 'sc.id';
    $orderDir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';

    $query .= " ORDER BY {$orderColumn} {$orderDir}";
} else {
    $query .= " ORDER BY sc.id DESC";
}

$length = (int)($_POST['length'] ?? 10);
$start = (int)($_POST['start'] ?? 0);

if ($length != -1) {
    $query .= " LIMIT :start, :length";
}

$stmt = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
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
    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'] ?? '';
    $sub_array[] = $row['staff_name'] ?? '';
    $sub_array[] = $staff_type[$row['staff_type']] ?? '';
    $sub_array[] = $gender_type[$row['gender']] ?? '';
    $sub_array[] = $row['place'] ?? '';
    $sub_array[] = $row['mobile1'] ?? '';
    $sub_array[] = $row['email'] ?? '';
    $sub_array[] = !empty($row['joining_date'])
        ? date('d-m-Y', strtotime($row['joining_date']))
        : '';
    $sub_array[] = $row['company_name'] ?? '';
    $sub_array[] = $row['branch_name'] ?? '';
    $sub_array[] = $row['department_name'] ?? '';
    $sub_array[] = $row['team_name'] ?? '';
    $sub_array[] = $office_type[$row['off_type']] ?? '';
    $sub_array[] = $row['designation'] ?? '';
    $sub_array[] = $row['reporting_person'] ?? '';
    $sub_array[] = (!empty($row['relieve_date']) && $row['relieve_date'] != '0000-00-00')
        ? date('d-m-Y', strtotime($row['relieve_date']))
        : '';
    $sub_array[] = $pf_available_type[$row['pf_available']] ?? '';
    $sub_array[] = $esi_available_type[$row['esi_available']] ?? '';
    $sub_array[] = $pt_available_type[$row['pt_available']] ?? '';
    $sub_array[] = $row['shift_name'] ?? '';
    $sub_array[] = moneyFormatIndia($row['total_ctc']);
    $sub_array[] = moneyFormatIndia($row['annual_ctc']);
    $sub_array[] = $status_type[$row['status']] ?? '';

    $data[] = $sub_array;
}

$output = [
    "draw" => intval($_POST['draw'] ?? 0),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
];

echo json_encode($output);
?>