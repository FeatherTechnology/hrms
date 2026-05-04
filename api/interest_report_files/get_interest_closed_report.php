<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$sub_status = ['' => '', 1 => 'Consider', 2 => 'Reject'];

$column = array(
    'cs.id',
    'lnc.linename',
    'lelc.loan_id',
    'li.issue_date',
    'cp.cus_id',
    'cp.aadhar_num',
    'cp.cus_name',
    'anc.areaname',
    'bc.branch_name',
    'cp.mobile1',
    'lc.loan_category',
    'lelc.loan_amnt',
    'lelc.maturity_date',
    'cs.closed_date',
    'cs.status',
    'cs.sub_status'
);


$query = "SELECT cs.id, lnc.linename, lelc.loan_id, li.issue_date, cp.cus_id, cp.aadhar_num, cp.cus_name, anc.areaname, bc.branch_name , cp.mobile1, lc.loan_category, lelc.loan_amnt, lelc.maturity_date, cs.closed_date, cs.status, cs.sub_status 
FROM customer_status cs
JOIN loan_issue li ON cs.cus_profile_id = li.cus_profile_id 
JOIN customer_profile cp ON cs.cus_profile_id = cp.id
JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
JOIN line_name_creation lnc ON cp.line = lnc.id
JOIN area_name_creation anc ON cp.area = anc.id
JOIN area_creation ac ON cp.line = ac.line_id
JOIN branch_creation bc ON ac.branch_id = bc.id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
WHERE cs.status > 8 AND u.id ='$user_id' AND lcc.due_type = 'interest' AND us.id ='$user_id' AND li.balance_amount = 0 AND cs.closed_date BETWEEN '$from_date' AND '$to_date'";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (anc.areaname LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_id LIKE '%" . $_POST['search'] . "%' OR
            li.issue_date LIKE '%" . $_POST['search'] . "%' OR
            cp.cus_id LIKE '%" . $_POST['search'] . "%' OR
            cp.aadhar_num LIKE '%" . $_POST['search'] . "%' OR
            cp.cus_name LIKE '%" . $_POST['search'] . "%' OR
            lnc.linename LIKE '%" . $_POST['search'] . "%' OR
            lc.loan_category LIKE '%" . $_POST['search'] . "%' OR
            lelc.maturity_date LIKE '%" . $_POST['search'] . "%' OR
            cs.closed_date LIKE '%" . $_POST['search'] . "%' OR
            bc.branch_name LIKE '%" . $_POST['search'] . "%' ) ";
    }
}

if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}

$query1 = "";
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
    $sub_array[] = $row['linename'];
    $sub_array[] = $row['loan_id'];
    $sub_array[] = date('d-m-Y', strtotime($row['issue_date']));
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['mobile1'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = moneyFormatIndia($row['loan_amnt']);
    $sub_array[] = date('d-m-Y', strtotime($row['maturity_date']));
    $sub_array[] = date('d-m-Y', strtotime($row['closed_date']));
    $sub_array[] = $sub_status[$row['sub_status']];

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

function count_all_data($pdo)
{
    $query = "SELECT cs.id FROM customer_status cs LEFT JOIN loan_entry_loan_calculation lelc ON cs.cus_profile_id = lelc.cus_profile_id where status > 8 AND lelc.due_type = 'Interest' ";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
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
