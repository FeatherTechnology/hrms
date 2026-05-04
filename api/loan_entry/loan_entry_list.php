<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$column = array(
    'cp.id',
    'cp.cus_id',
    'cp.aadhar_num',
    'cp.cus_name',
    'lelc.loan_id',
    'lc.loan_category',
    'lelc.loan_amount',
    'anc.areaname',
    'lnc.linename',
    'bc.branch_name',
    'cp.mobile1',
    'cp.id'
);

$query = "SELECT cp.id, cp.cus_id, cp.cus_name, cp.aadhar_num, lelc.loan_id, lc.loan_category, lelc.loan_amount,lelc.loan_date, anc.areaname, lnc.linename, bc.branch_name , cp.mobile1,cp.cus_data, lelc.id as loan_calc_id, cs.id as cus_sts_id, cs.status as c_sts
FROM customer_profile cp
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
WHERE cp.insert_login_id = '$user_id' AND cs.status <= 2 ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (cp.cus_id LIKE '" . $search . "%'
                      OR cp.aadhar_num LIKE '%" . $search . "%'
                      OR cp.cus_name LIKE '%" . $search . "%'
                      OR lelc.loan_id LIKE '%" . $search . "%'
                      OR lc.loan_category LIKE '%" . $search . "%'
                      OR anc.areaname LIKE '%" . $search . "%'
                      OR lnc.linename LIKE '%" . $search . "%'
                      OR bc.branch_name LIKE '%" . $search . "%'
                      OR cp.mobile1 LIKE '%" . $search . "%')";
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
    $sub_array[] = isset($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['cus_id']) ? $row['cus_id'] : '';
    $sub_array[] = isset($row['aadhar_num']) ? $row['aadhar_num'] : '';
    $sub_array[] = isset($row['cus_name']) ? $row['cus_name'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['linename']) ? $row['linename'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['loan_amount']) ? moneyFormatIndia($row['loan_amount']) : '';
    $sub_array[] = isset($row['cus_data']) ? $row['cus_data'] : '';

    $action = "<div class='dropdown'>
                <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
                <div class='dropdown-content'>";

    $action .= "<a href='#' class='edit-loan-entry' value='" . $row['id'] . "' data-id='" . $row['loan_calc_id'] . "' data-sts='" . $row['c_sts'] . "' title='Edit details'>Edit</a>";

    if ($row['c_sts'] == '2') {
        $action  .= "<a href='#' class='move-loan-entry' value='" . $row['cus_sts_id'] . "' title='Move'>Move</a>";
    }

    $action .= "</div></div>";
    $sub_array[] = $action;
    $data[] = $sub_array;
}
function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM customer_profile";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
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

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
