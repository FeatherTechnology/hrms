<?php 
require "../../ajaxconfig.php"; 
@session_start(); 
 
$user_id = $_SESSION['user_id'] ?? 0; 
 
$userQry = $pdo->prepare("SELECT id,report_access,user_type,director_name,staff_name_id FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$user = $userQry->fetch(PDO::FETCH_ASSOC);

$report_access = $user['report_access'] ?? 1;
$user_type     = $user['user_type'] ?? '';
$director_id   = $user['director_name'] ?? '';
$staff_id      = $user['staff_name_id'] ?? '';
 
$from_date     = $_POST['params']['from_date'] ?? ''; 
$to_date       = $_POST['params']['to_date'] ?? ''; 
$company_id    = $_POST['params']['company_id'] ?? ''; 
$department_id = $_POST['params']['department_id'] ?? ''; 
$title         = $_POST['params']['title'] ?? ''; 
$question      = $_POST['params']['question'] ?? ''; 
 
$column = [ 
    'ssf.id', 
    'sc.staff_id', 
    'sc.staff_name', 
    'dc.department_name', 
    'ft.feedback_title', 
    'fqm.feedback_questions', 
    'ssf.answer', 
    'ssf.created_date'
]; 

$allowedStaffIds = [];
$params = [];

if ($report_access == 2) {

    if ($user_type == 1 && !empty($director_id)) {

        $hierarchyStmt = $pdo->prepare("SELECT
                rpm.reporting_staff,
                rp.user_type,
                rp.director_id,
                rp.reporting_person
            FROM reporting_person_mapping rpm
            INNER JOIN reporting_person rp
                ON rp.id = rpm.reporting_person_id");

        $hierarchyStmt->execute();
        $hierarchyRows = $hierarchyStmt->fetchAll(PDO::FETCH_ASSOC);

        $directStaff = [];
        $staffChildren = [];

        foreach ($hierarchyRows as $row) {

            $childStaff = (int)$row['reporting_staff'];

            if ($childStaff <= 0) {
                continue;
            }

            if (
                (int)$row['user_type'] === 1 &&
                (string)$row['director_id'] === (string)$director_id
            ) {
                $directStaff[] = $childStaff;
            }

            if (
                (int)$row['user_type'] === 2 &&
                !empty($row['reporting_person'])
            ) {
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
            INNER JOIN reporting_person rp
                ON rp.id = rpm.reporting_person_id
            WHERE rp.user_type = 2
            AND rp.reporting_person = :staff_id";

        if (!empty($company_id)) {
            $staffSql .= " AND rp.company_id = :access_company_id";
        }

        $staffStmt = $pdo->prepare($staffSql);

        $staffParams = [
            ':staff_id' => $staff_id
        ];

        if (!empty($company_id)) {
            $staffParams[':access_company_id'] = $company_id;
        }

        $staffStmt->execute($staffParams);

        $allowedStaffIds = $staffStmt->fetchAll(PDO::FETCH_COLUMN);
        $allowedStaffIds = array_map('intval', $allowedStaffIds);
    }
}

$baseQuery = " 
FROM staff_sch_feedback ssf 
LEFT JOIN feedback_titles ft 
    ON ft.id = ssf.feedback_titles_id	 
LEFT JOIN users u 
    ON u.id = ssf.insert_login_id	 
LEFT JOIN staff_creation sc 
    ON sc.id = u.staff_name_id 
LEFT JOIN occupation_info oi 
    ON oi.id = (
        SELECT MAX(id) 
        FROM occupation_info 
        WHERE staff_profile_id = u.staff_name_id
    ) 
LEFT JOIN department_creation dc 
    ON dc.id = oi.department 
LEFT JOIN feedback_questions_mapping fqm 
    ON fqm.id = ssf.feedback_ques_map_id 
WHERE 1 = 1 
"; 

if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {

    if (!empty($allowedStaffIds)) {

        $accessPlaceholders = [];

        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':access_staff_' . $index;
            $accessPlaceholders[] = $placeholder;
            $params[$placeholder] = (int)$staffId;
        }

        $baseQuery .= " AND sc.id IN (" . implode(',', $accessPlaceholders) . ")";

    } else {
        $baseQuery .= " AND 1 = 0";
    }
}

if (!empty($company_id)) { 
    $baseQuery .= " AND ft.company_id = :company_id "; 
    $params[':company_id'] = $company_id; 
} 

if (!empty($department_id)) {
    $baseQuery .= " AND oi.department = :department_id ";
    $params[':department_id'] = $department_id;
}
 
if (!empty($title)) { 
    $baseQuery .= " AND ssf.feedback_titles_id = :title "; 
    $params[':title'] = $title; 
} 
 
if (!empty($question)) { 
    $baseQuery .= " AND ssf.feedback_ques_map_id = :question "; 
    $params[':question'] = $question; 
} 
 
if (!empty($from_date) && !empty($to_date)) { 
    $baseQuery .= " AND DATE(ssf.created_date) BETWEEN :from_date AND :to_date "; 
    $params[':from_date'] = $from_date;
    $params[':to_date'] = $to_date;
} 
 
if (!empty($_POST['search']['value'])) { 
 
    $search = trim($_POST['search']['value']); 
 
    $baseQuery .= " 
    AND ( 
        sc.staff_id LIKE :search 
        OR sc.staff_name LIKE :search 
        OR ft.feedback_title LIKE :search 
        OR fqm.feedback_questions LIKE :search
        OR ssf.answer LIKE :search
    )"; 
 
    $params[':search'] = "%{$search}%"; 
} 
 
$query = " 
SELECT 
    ssf.id, 
    sc.staff_id, 
    sc.staff_name, 
    dc.department_name, 
    ft.feedback_title, 
    fqm.feedback_questions, 
    ssf.answer, 
    ssf.created_date 
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
 
$totalQuery = "SELECT COUNT(*) FROM staff_sch_feedback ssf
LEFT JOIN feedback_titles ft ON ft.id = ssf.feedback_titles_id
LEFT JOIN users u ON u.id = ssf.insert_login_id
LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
LEFT JOIN occupation_info oi 
    ON oi.id = (
        SELECT MAX(id) 
        FROM occupation_info 
        WHERE staff_profile_id = u.staff_name_id
    )
WHERE 1 = 1";

$totalParams = [];

if ($report_access == 2 && ($user_type == 1 || $user_type == 2)) {

    if (!empty($allowedStaffIds)) {

        $totalAccessPlaceholders = [];

        foreach ($allowedStaffIds as $index => $staffId) {
            $placeholder = ':total_access_staff_' . $index;
            $totalAccessPlaceholders[] = $placeholder;
            $totalParams[$placeholder] = (int)$staffId;
        }

        $totalQuery .= " AND sc.id IN (" . implode(',', $totalAccessPlaceholders) . ")";

    } else {
        $totalQuery .= " AND 1 = 0";
    }
}

$stmt = $pdo->prepare($totalQuery);

foreach ($totalParams as $key => $value) {
    $stmt->bindValue(
        $key,
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
    );
}

$stmt->execute();
$recordsTotal = (int)$stmt->fetchColumn();
 
if (isset($_POST['order']) && isset($_POST['order'][0])) { 
 
    $orderIndex = (int)$_POST['order'][0]['column'];
    $orderColumn = $column[$orderIndex] ?? 'ssf.id';
    $orderDir = (strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC'; 
 
    $query .= " ORDER BY {$orderColumn} {$orderDir}"; 
} else { 
    $query .= " ORDER BY ssf.id DESC"; 
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
    $sub_array[] = $row['department_name'] ?? ''; 
    $sub_array[] = $row['feedback_title'] ?? ''; 
    $sub_array[] = $row['feedback_questions'] ?? ''; 
    $sub_array[] = $row['answer'] ?? ''; 
    $sub_array[] = !empty($row['created_date']) 
        ? date('d-m-Y', strtotime($row['created_date'])) 
        : ''; 
 
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