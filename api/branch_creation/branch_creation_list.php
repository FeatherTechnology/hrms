<?php
require '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$user_query = $pdo->prepare("SELECT user_type, director_company, company_id FROM users WHERE id = ?");
$user_query->execute([$user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

$column = array(
    'bc.id',
    'bc.branch_code',
    'cc.company_name',
    'bc.branch_name',
    'bc.place',
    'st.state_name',
    'dt.district_name',
    'bc.mobile_number',
    'bc.email_id',
    'bc.id'
);

$query = "SELECT bc.id,bc.branch_code,cc.company_name,bc.branch_name,bc.place,st.state_name,dt.district_name,bc.mobile_number,bc.email_id
FROM branch_creation bc
LEFT JOIN States st ON bc.state = st.id
LEFT JOIN districts dt ON bc.district = dt.id
LEFT JOIN company_creation cc ON bc.company_id = cc.id
WHERE 1";
$params = [];

if ($user) {
    if ((int)$user['user_type'] === 1) {
        $company_ids = array_filter(array_map('intval', explode(',', trim($user['director_company'] ?? ''))), fn($id) => $id > 0);
       
            $placeholders = implode(',', array_fill(0, count($company_ids), '?'));
            $query .= " AND bc.company_id IN ($placeholders)";
            $params = array_values($company_ids);
    } elseif ((int)$user['user_type'] === 2) {
        $company_id = (int)($user['company_id'] ?? 0);
            $query .= " AND bc.company_id = ?";
            $params[] = $company_id;
    } 
} else {
    $query .= " AND 1 = 0";
}

$search = trim($_POST['search']['value'] ?? $_POST['search'] ?? '');
if ($search !== '') {
    $query .= " AND (bc.branch_code LIKE ? OR cc.company_name LIKE ? OR bc.branch_name LIKE ? OR bc.place LIKE ? OR st.state_name LIKE ? OR dt.district_name LIKE ? OR bc.mobile_number LIKE ? OR bc.email_id LIKE ?)";
    $search_params = [
        $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%',
        '%' . $search . '%'
    ];
    $params = array_merge($params, $search_params);
}

$statement = $pdo->prepare($query);
$statement->execute($params);
$number_filter_row = $statement->rowCount();

if (isset($_POST['order'][0]['column'])) {
    $order_column = (int)$_POST['order'][0]['column'];
    $order_dir = strtolower($_POST['order'][0]['dir'] ?? 'asc');
    $order_dir = $order_dir === 'desc' ? 'DESC' : 'ASC';
    if (isset($column[$order_column])) {
        $query .= " ORDER BY " . $column[$order_column] . " " . $order_dir;
    }
}

$query1 = '';
if (isset($_POST['length']) && (int)$_POST['length'] !== -1) {
    $start = max(0, (int)($_POST['start'] ?? 0));
    $length = max(0, (int)$_POST['length']);
    $query1 = " LIMIT $start, $length";
}

$statement = $pdo->prepare($query . $query1);
$statement->execute($params);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement->execute();

$result = $statement->fetchAll();
$sno = isset($_POST['start']) ? $_POST['start'] + 1 : 1;
$data = [];

foreach ($result as $row) {
    $sub_array = array();

    $sub_array[] = $sno++;
    $sub_array[] = isset($row['branch_code']) ? $row['branch_code'] : '';
    $sub_array[] = isset($row['company_name']) ? $row['company_name'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['place']) ? $row['place'] : '';
    $sub_array[] = isset($row['state_name']) ? $row['state_name'] : '';
    $sub_array[] = isset($row['district_name']) ? $row['district_name'] : '';
    $sub_array[] = isset($row['mobile_number']) ? $row['mobile_number'] : '';
    $sub_array[] = isset($row['email_id']) ? $row['email_id'] : '';

    $action = "<span class='icon-border_color branchActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete branchDeleteBtn' value='" . $row['id'] . "'></span>";
    $sub_array[] = $action;

    $data[] = $sub_array;
}

function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM branch_creation";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
?>