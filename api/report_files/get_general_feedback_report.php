<?php 
require "../../ajaxconfig.php"; 
@session_start(); 

$user_id = $_SESSION['user_id'] ?? 0;

$userQry = $pdo->prepare("SELECT id,feedback_access_type FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$user = $userQry->fetch(PDO::FETCH_ASSOC);

$feedback_access_type = $user['feedback_access_type'] ?? 1;

$from_date  = $_POST['params']['from_date'] ?? ''; 
$to_date    = $_POST['params']['to_date'] ?? ''; 
$company_id = $_POST['params']['company_id'] ?? ''; 
$title      = $_POST['params']['title'] ?? ''; 

$column = [ 
    'sgf.id', 
    'sc.staff_id', 
    'sc.staff_name', 
    'gf.feedback_name', 
    'sgf.commants', 
    'sgf.attachment', 
    'sgf.created_date'
]; 

$baseQuery = "FROM staff_general_feedback sgf 
LEFT JOIN general_feedback gf ON gf.id = sgf.general_feedback_id
LEFT JOIN users u ON u.id = sgf.insert_login_id
LEFT JOIN staff_creation sc ON sc.id = u.staff_name_id
WHERE 1 = 1"; 

$params = [];

if ($feedback_access_type == 2) {
    $baseQuery .= " AND sgf.user_id = :logged_user_id";
    $params[':logged_user_id'] = $user_id;
}

if (!empty($company_id)) { 
    $baseQuery .= " AND gf.company_id = :company_id"; 
    $params[':company_id'] = $company_id; 
} 

if (!empty($title)) { 
    $baseQuery .= " AND sgf.general_feedback_id = :title"; 
    $params[':title'] = $title; 
} 

if (!empty($from_date) && !empty($to_date)) { 
    $baseQuery .= " AND DATE(sgf.created_date) BETWEEN :from_date AND :to_date";
    $params[':from_date'] = $from_date;
    $params[':to_date'] = $to_date;
} 

if (!empty($_POST['search']['value'])) { 

    $search = trim($_POST['search']['value']); 

    $baseQuery .= " AND (
        sc.staff_id LIKE :search 
        OR sc.staff_name LIKE :search 
        OR gf.feedback_name LIKE :search 
        OR sgf.commants LIKE :search)"; 

    $params[':search'] = "%{$search}%"; 
} 

$query = "SELECT 
    sgf.id, 
    sc.staff_id, 
    sc.staff_name, 
    gf.feedback_name, 
    sgf.commants, 
    sgf.attachment, 
    sgf.created_date
" . $baseQuery; 

$stmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);

foreach ($params as $key => $value) {
    $stmt->bindValue($key,$value,is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}

$stmt->execute(); 
$recordsFiltered = (int)$stmt->fetchColumn(); 

$totalQuery = "SELECT COUNT(*) FROM staff_general_feedback sgf";

$totalParams = [];

if ($feedback_access_type == 2) {
    $totalQuery .= " WHERE sgf.user_id = :total_user_id";
    $totalParams[':total_user_id'] = $user_id;
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
    $orderColumn = $column[$orderIndex] ?? 'sgf.id';
    $orderDir = strtolower($_POST['order'][0]['dir'] ?? '') == 'asc' ? 'ASC' : 'DESC'; 

    $query .= " ORDER BY {$orderColumn} {$orderDir}"; 
} else { 
    $query .= " ORDER BY sgf.id DESC"; 
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
    $sub_array[] = $row['feedback_name'] ?? ''; 
    $sub_array[] = $row['commants'] ?? ''; 
    $sub_array[] = $row['attachment'] ?? ''; 
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
?>