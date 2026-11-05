<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? 0;

$column = array(
    'ts.id',
    'cc.company_name',
    'bc.branch_name',
    'dc.department_name',
    'sc.staff_name',
    'ts.effective_month',
    'ts.id',
    'ts.id'
);

$query = "SELECT
    ts.*,
    GROUP_CONCAT(DISTINCT dc.department_name ORDER BY dc.department_name SEPARATOR ', ') AS department_name,
    GROUP_CONCAT(DISTINCT bc.branch_name ORDER BY bc.branch_name SEPARATOR ', ') AS branch_name,
    cc.company_name,
    GROUP_CONCAT(DISTINCT sc.staff_name ORDER BY sc.staff_name SEPARATOR ', ') AS staff_name,
    CASE
        WHEN COUNT(tsd.id) > 0
        AND SUM(
            CASE
                WHEN tsd.final_incentive IS NOT NULL
                AND tsd.final_incentive != ''
                THEN 1
                ELSE 0
            END
        ) = COUNT(tsd.id)
        THEN 'Completed'
        ELSE 'Pending'
    END AS evaluation_status
FROM target_setup ts
LEFT JOIN company_creation cc ON ts.company_id = cc.id
LEFT JOIN target_department_mapping tdm ON ts.id = tdm.target_setup_id
LEFT JOIN department_creation dc ON dc.id = tdm.department_id
LEFT JOIN target_branch_mapping rbm ON ts.id = rbm.target_setup_id
LEFT JOIN branch_creation bc ON bc.id = rbm.branch_id
LEFT JOIN target_setup_details tsd ON tsd.target_setup_id = ts.id
LEFT JOIN staff_creation sc ON sc.id = tsd.staff_id
GROUP BY ts.id
HAVING SUM(
    CASE
        WHEN tsd.achieved_value IS NOT NULL
        AND tsd.achieved_value != ''
        THEN 1
        ELSE 0
    END
) > 0";

/* Search */
if (!empty($_POST['search'])) {
    $search = $_POST['search'];

    $query .= " AND (
        staff_name LIKE :search
        OR company_name LIKE :search
        OR branch_name LIKE :search
        OR department_name LIKE :search
        OR ts.effective_month LIKE :search
        OR evaluation_status LIKE :search
    )";
}

/* Order */
if (isset($_POST['order'])) {
    $order_column = (int)$_POST['order'][0]['column'];
    $order_dir = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';

    $query .= " ORDER BY " . $column[$order_column] . " " . $order_dir;
} else {
    $query .= " ORDER BY ts.id DESC";
}

/* Count filtered records */
$statement = $pdo->prepare($query);

if (!empty($_POST['search'])) {
    $statement->bindValue(':search', '%' . $_POST['search'] . '%');
}

$statement->execute();
$number_filter_row = $statement->rowCount();

/* Limit */
$query1 = '';

if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = (int)($_POST['start'] ?? 0);
    $length = (int)$_POST['length'];

    $query1 = " LIMIT $start, $length";
}

/* Execute */
$statement = $pdo->prepare($query . $query1);

if (!empty($_POST['search'])) {
    $statement->bindValue(':search', '%' . $_POST['search'] . '%');
}

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = (int)($_POST['start'] ?? 0) + 1;

foreach ($result as $row) {

    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = date('F Y', strtotime($row['effective_month']));
    $sub_array[] = $row['evaluation_status'];

 $sub_array[] = '<button type="button" class="btn btn-sm reviewBtn" value="' . $row['id'] . '" style="background-color:#f26b35; border-color:#f26b35; color:#fff;">Review</button>';

    $data[] = $sub_array;
}

/* Count Total */
function count_all_data($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM target_setup");
    return $stmt->fetchColumn();
}

/* Output */
$output = [
    "draw" => intval($_POST['draw'] ?? 0),
    "recordsTotal" => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
];

echo json_encode($output);
?>