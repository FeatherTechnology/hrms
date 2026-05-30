
<?php
require '../../ajaxconfig.php';

$column = array(
    'sc.id',
    'cc.company_name',
    'st.state_name',
    'sc.pf_applicable',
    'sc.esi_applicable',
    'sc.id'
);

$query = "SELECT sc.id as statutory_compliance_id, cc.company_name, st.state_name, sc.pf_applicable, sc.esi_applicable
FROM statutory_compliance sc 
LEFT JOIN company_creation cc ON sc.company_id = cc.id 
LEFT JOIN States st ON sc.state = st.id 
WHERE 1 ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (sc.id LIKE '" . $search . "%'
                      OR cc.company_name LIKE '%" . $search . "%'
                      OR sc.pf_applicable LIKE '%" . $search . "%'
                      OR sc.esi_applicable LIKE '%" . $search . "%'
                      OR st.state_name LIKE '%" . $search . "%')";
    }
}

if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}

$query1 = '';
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $query1 = ' LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}

$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();
$sno = isset($_POST['start']) ? $_POST['start'] + 1 : 1;
$data = [];

foreach ($result as $row) {
    $sub_array = array();

    $sub_array[] = $sno++;
    $sub_array[] = isset($row['company_name']) ? $row['company_name'] : '';
    $sub_array[] = isset($row['state_name']) ? $row['state_name'] : '';
    $sub_array[] = isset($row['pf_applicable']) ? $row['pf_applicable'] . '%' : '';
    $sub_array[] = isset($row['esi_applicable']) ? $row['esi_applicable'] . '%' : '';

    $action = "<span class='icon-border_color statutoryComplianceActionBtn' value='" . $row['statutory_compliance_id'] . "'></span>";
    $sub_array[] = $action;

    $data[] = $sub_array;
}

function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM statutory_compliance";
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