<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['params']['from_date'];
$to_date = $_POST['params']['to_date'];

$column = array(
    'li.id',
    'lelc.loan_id',
    'cp.cus_id',
    'cp.aadhar_num',
    'cp.cus_name',
    'gaurantor',
    'anc.areaname',
    'lnc.linename',
    'bc.branch_name',
    'cp.mobile1',
    'lc.loan_category',
    'agc.agent_name',
    'li.issue_date',
    'lelc.maturity_date',
    'lelc.loan_amnt',
    'lelc.principal_amnt',
    'lelc.interest_amnt',
    'lelc.doc_charge_calculate',
    'lelc.processing_fees_calculate',
    'lelc.total_amnt',
    'li.net_cash',
    'li.issue_person',
    'li.relationship'
);
$query = "SELECT lelc.loan_id, cp.cus_id, cp.aadhar_num, cp.cus_name, fi.fam_name as gaurantor, anc.areaname, lnc.linename, bc.branch_name , cp.mobile1, lc.loan_category, agc.agent_name, li.issue_date, lelc.loan_amnt, lelc.principal_amnt, lelc.interest_amnt, lelc.doc_charge_calculate, lelc.processing_fees_calculate, lelc.total_amnt, li.net_cash, li.issue_person, li.relationship , lelc.maturity_date
FROM loan_issue li 
JOIN customer_profile cp ON li.cus_profile_id = cp.id
JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN family_info fi ON cp.guarantor_name = fi.id
JOIN line_name_creation lnc ON cp.line = lnc.id
JOIN area_name_creation anc ON cp.area = anc.id
JOIN area_creation ac ON cp.line = ac.line_id
JOIN branch_creation bc ON ac.branch_id = bc.id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
WHERE u.id ='$user_id' AND us.id ='$user_id' AND lcc.due_type = 'emi' AND li.issue_date BETWEEN '$from_date' AND '$to_date' ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .=     " AND (lelc.loan_id LIKE '%" . $search . "%'
        OR cp.cus_id LIKE '%" . $search . "%'
        OR cp.aadhar_num LIKE '%" . $search . "%'
        OR cp.cus_name LIKE '%" . $search . "%'
        OR fi.fam_name LIKE '%" . $search . "%'
        OR anc.areaname LIKE '%" . $search . "%'
        OR lnc.linename LIKE '%" . $search . "%'
        OR bc.branch_name LIKE '%" . $search . "%'
        OR cp.mobile1 LIKE '%" . $search . "%' 
        OR lc.loan_category LIKE '%" . $search . "%' 
        OR agc.agent_name LIKE '%" . $search . "%' 
        OR li.issue_date LIKE '%" . $search . "%' 
        OR lelc.loan_amnt LIKE '%" . $search . "%' 
        OR lelc.principal_amnt LIKE '%" . $search . "%' 
        OR lelc.interest_amnt LIKE '%" . $search . "%' 
        OR lelc.doc_charge_calculate LIKE '%" . $search . "%' 
        OR lelc.processing_fees_calculate LIKE '%" . $search . "%' 
        OR lelc.total_amnt LIKE '%" . $search . "%' 
        OR li.net_cash LIKE '%" . $search . "%' 
        OR li.issue_person LIKE '%" . $search . "%' 
        OR li.relationship LIKE '%" . $search . "%' )";
    }
}

$query .= "GROUP BY li.cus_profile_id";

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
    $sub_array[] = isset($row['loan_id']) ? $row['loan_id'] : '';
    $sub_array[] = isset($row['cus_id']) ? $row['cus_id'] : '';
    $sub_array[] = isset($row['aadhar_num']) ? $row['aadhar_num'] : ''; 
    $sub_array[] = isset($row['cus_name']) ? $row['cus_name'] : '';
    $sub_array[] = isset($row['gaurantor']) ? $row['gaurantor'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['linename']) ? $row['linename'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['agent_name']) ? $row['agent_name'] : '';
    $sub_array[] = isset($row['issue_date']) ? date('d-m-Y', strtotime($row['issue_date'])) : '';
    $sub_array[] = isset($row['maturity_date']) ? date('d-m-Y', strtotime($row['maturity_date'])) : '';
    $sub_array[] = isset($row['loan_amnt']) ? moneyFormatIndia($row['loan_amnt']) : '';
    $sub_array[] = isset($row['principal_amnt']) ? moneyFormatIndia($row['principal_amnt']) : '';
    $sub_array[] = isset($row['interest_amnt']) ? moneyFormatIndia($row['interest_amnt']) : '';
    $sub_array[] = isset($row['doc_charge_calculate']) ? moneyFormatIndia($row['doc_charge_calculate']) : '';
    $sub_array[] = isset($row['processing_fees_calculate']) ? moneyFormatIndia($row['processing_fees_calculate']) : '';
    $sub_array[] = isset($row['total_amnt']) ? moneyFormatIndia($row['total_amnt']) : '';
    $sub_array[] = isset($row['net_cash']) ? moneyFormatIndia($row['net_cash']) : '';
    $sub_array[] = isset($row['issue_person']) ? $row['issue_person'] : '';
    $sub_array[] = isset($row['relationship']) ? $row['relationship'] : '';

    $data[] = $sub_array;
}
function count_all_data($pdo)
{
    $query = "SELECT li.id FROM loan_issue li LEFT JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id WHERE lelc.due_type = 'EMI' ";
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

function moneyFormatIndia($num)
{
    $isNegative = false;
    if ($num < 0) {
        $isNegative = true;
        $num = abs($num);
    }

    $explrestunits = "";
    if (strlen((string)$num) > 3) {
        $lastthree = substr((string)$num, -3);
        $restunits = substr((string)$num, 0, -3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        foreach ($expunit as $index => $value) {
            if ($index == 0) {
                $explrestunits .= (int)$value . ",";
            } else {
                $explrestunits .= $value . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    $thecash = $isNegative ? "-" . $thecash : $thecash;
    $thecash = $thecash == 0 ? "" : $thecash;
    return $thecash;
}
