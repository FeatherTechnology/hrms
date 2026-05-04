<?php
include '../../ajaxconfig.php';

@session_start();
$user_id = $_SESSION['user_id'];

$where = "1";

if (isset($_POST['from_date']) && isset($_POST['to_date']) && $_POST['from_date'] != '' && $_POST['to_date'] != '') {
    $from_date = date('Y-m-d', strtotime($_POST['from_date']));
    $to_date = date('Y-m-d', strtotime($_POST['to_date']));
    $where  = "(date(cs.updated_on) >= '" . $from_date . "') and (date(cs.updated_on) <= '" . $to_date . "') ";
}

$statusLabels = [
    '5' => "Cancel - Approval",
    '6' => 'Revoke - Approval',
    '13' => 'Cancel - Loan Issue',
    '14' => 'Revoke - Loan Issue',
];

// Check if type and sel_screen are selected by the user
if (isset($_POST['type']) && isset($_POST['sel_screen'])) {
    $type = $_POST['type'];
    $sel_screen = $_POST['sel_screen'];

    // Determine cus_status based on type and sel_screen
    if ($type == '1') { // '1' for Cancel
        switch ($sel_screen) {
            case '1':
                $cus_status = '5'; // Cancel - Approval
                break;
            case '2':
                $cus_status = '13'; // Cancel - Loan Issue
                break;
        }
    } elseif ($type == '2') { // '2' for Revoke
        switch ($sel_screen) {
            case '1':
                $cus_status = '6'; // Revoke - Approval
                break;
            case '2':
                $cus_status = '14'; // Revoke - Loan Issue
                break;
        }
    }
}   // Append the cus_status condition if it's set

$column = array(
    'cp.id',
    'cp.aadhar_num',
    'cp.cus_id',
    'cp.cus_name',
    'anc.areaname',
    'lc.loan_category',
    'lelc.loan_amnt',
    'r.role',
    'u.name',
    'agc.agent_name',
    'cp.cus_data',
    'cs.updated_on',
    'cs.status',
    'cp.remark'
);

$query = "SELECT cp.aadhar_num, cp.cus_id, cp.cus_name, anc.areaname, lc.loan_category, lelc.loan_amnt, r.role, u.name, agc.agent_name, 
cp.cus_data, cs.status, cp.remark, cs.updated_on
FROM 
    customer_profile cp 
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
    JOIN users u ON u.id = cs.update_login_id
    LEFT JOIN role r ON u.role = r.id
    LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
WHERE 
    $where AND cs.status = '$cus_status' AND lelc.due_type = 'EMI' ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {

        $query .= " and (cp.aadhar_num LIKE '%" . $_POST['search'] . "%' OR
                cp.cus_id LIKE '%" . $_POST['search'] . "%' OR
                cp.cus_name LIKE '%" . $_POST['search'] . "%' OR
                anc.areaname LIKE '%" . $_POST['search'] . "%' OR
                lc.loan_category LIKE '%" . $_POST['search'] . "%' OR
                lelc.loan_amnt LIKE '%" . $_POST['search'] . "%' OR
                r.role LIKE '%" . $_POST['search'] . "%' OR
                u.name LIKE '%" . $_POST['search'] . "%' OR
                agc.agent_name LIKE '%" . $_POST['search'] . "%' OR
                cp.cus_data LIKE '%" . $_POST['search'] . "%' OR
                cs.status LIKE '%" . $_POST['search'] . "%' OR
                cp.remark LIKE '%" . $_POST['search'] . "%' ) ";
    }
}


if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}

$query1 = '';
if ($_POST['length'] != -1) {
    $query1 = " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
}

$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$sno = 1;
foreach ($result as $row) {
    $sub_array   = array();

    $sub_array[] = $sno;
    $sub_array[] = isset($row['aadhar_num']) ? $row['aadhar_num'] : '';
    $sub_array[] = isset($row['cus_id']) ? $row['cus_id'] : '';
    $sub_array[] = isset($row['cus_name']) ? $row['cus_name'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['loan_amnt']) ? moneyFormatIndia($row['loan_amnt']) : '';
    $sub_array[] = $row['role'];
    $sub_array[] = $row['name'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['cus_data'];
    $sub_array[] = date('d-m-Y', strtotime($row['updated_on']));
    $sub_array[] = $statusLabels[$row['status']];
    $sub_array[] = $row['remark'];

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);

function count_all_data($connect)
{
    $query     = "SELECT cp.id FROM customer_profile cp LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id WHERE lelc.due_type = 'EMI' ";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash;
}

// Close the database connection
$connect = null;
