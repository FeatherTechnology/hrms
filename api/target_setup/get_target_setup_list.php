<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];

$column = array(
    'ts.id',
    'cc.company_name',
    'bc.branch_name',
    'dc.department_name',
    'sc.staff_name',
    'ts.effective_month',
    'ts.id'
);


$query = "SELECT
    ts.*,
    GROUP_CONCAT(DISTINCT dc.department_name ORDER BY dc.department_name SEPARATOR ', ') AS department_name,
    GROUP_CONCAT(DISTINCT bc.branch_name ORDER BY bc.branch_name SEPARATOR ', ') AS branch_name,
    cc.company_name,
    GROUP_CONCAT(DISTINCT sc.staff_name ORDER BY sc.staff_name SEPARATOR ', ') AS staff_name,
    CASE
        WHEN EXISTS (
            SELECT 1
            FROM target_setup_details eval
            WHERE eval.target_setup_id = ts.id
            AND eval.achieved_value IS NOT NULL
            AND eval.achieved_value != ''
        ) THEN 1
        ELSE 0
    END AS is_evaluated
FROM target_setup ts
LEFT JOIN company_creation cc ON ts.company_id = cc.id
LEFT JOIN target_department_mapping tdm ON ts.id = tdm.target_setup_id
LEFT JOIN department_creation dc ON dc.id = tdm.department_id
LEFT JOIN target_branch_mapping rbm ON ts.id = rbm.target_setup_id
LEFT JOIN branch_creation bc ON bc.id = rbm.branch_id
LEFT JOIN target_setup_details tsd ON tsd.target_setup_id = ts.id
LEFT JOIN staff_creation sc ON sc.id = tsd.staff_id
WHERE 1=1
";

/* Search */
if (!empty($_POST['search'])) {
    $search = $_POST['search'];
    $query .= " AND (
        sc.staff_name LIKE :search1
        OR cc.company_name LIKE :search2
        OR bc.branch_name LIKE :search3
        OR dc.department_name LIKE :search4
        OR ts.effective_month LIKE :search5
    )";
}

$query .= " GROUP BY ts.id ";

/* Order */
if (isset($_POST['order'])) {
    $query .= " ORDER BY " .
        $column[$_POST['order'][0]['column']] . " " .
        ($_POST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC');
} else {
    $query .= " ORDER BY ts.id DESC ";
}

/* Limit */
$query1 = '';

if ($_POST['length'] != -1) {
    $query1 = " LIMIT " . (int)$_POST['start'] . "," . (int)$_POST['length'];
}

/* Execute */
$statement = $pdo->prepare($query);

if (!empty($_POST['search'])) {
    $searchValue = '%' . $_POST['search'] . '%';

    for ($i = 1; $i <= 5; $i++) {
        $statement->bindValue(":search$i", $searchValue);
    }
}

$statement->execute();
$filteredRows = $statement->fetchAll(PDO::FETCH_ASSOC);
$number_filter_row = count($filteredRows);

/* Fetch paginated records */
$statement = $pdo->prepare($query . $query1);

if (!empty($_POST['search'])) {
    $searchValue = '%' . $_POST['search'] . '%';

    for ($i = 1; $i <= 5; $i++) {
        $statement->bindValue(":search$i", $searchValue);
    }
}

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = (int)$_POST['start'] + 1;

foreach ($result as $row) {

    $sub_array = [];
    $isEvaluated = (int)$row['is_evaluated'] === 1;

    $sub_array[] = $sno++;
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = date('F Y', strtotime($row['effective_month']));

    if ($isEvaluated) {
        $sub_array[] = "
        <span class='icon-border_color targetSetupActionBtn'
            value='" . $row['id'] . "' title='Edit'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete text-muted'
            title='Delete disabled: Staff evaluation has started'
            style='opacity:0.4; cursor:not-allowed; pointer-events:none;'>
        </span>
    ";
    } else {
        $sub_array[] = "
        <span class='icon-border_color targetSetupActionBtn'
            value='" . $row['id'] . "' title='Edit'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete targetSetupDeleteBtn'
            value='" . $row['id'] . "' title='Delete'></span>
    ";
    }

    $data[] = $sub_array;
}

/* Count Total */
function count_all_data($pdo)
{
    return $pdo->query("SELECT COUNT(*) FROM target_setup")->fetchColumn();
}

/* Output */
$output = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
];

echo json_encode($output);
