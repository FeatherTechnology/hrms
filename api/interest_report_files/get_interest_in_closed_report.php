<?php
include '../../ajaxconfig.php';

$where = "";
$from_date = "";
$to_date = "";

if (isset($_POST['from_date']) && isset($_POST['to_date']) && $_POST['from_date'] != '' && $_POST['to_date'] != '') {
    $from_date = date('Y-m-d', strtotime($_POST['from_date']));
    $to_date = date('Y-m-d', strtotime($_POST['to_date']));
    $where  = " AND cs.in_closed_date BETWEEN '$from_date' AND '$to_date' AND DATE(cs.closed_date) NOT BETWEEN '$from_date' AND '$to_date'";
}

$column = array(
    'cp.id',
    'lnc.linename',
    'lelc.loan_id',
    'lelc.loan_date',
    'cp.aadhar_num',
    'cp.cus_id',
    'cp.cus_name',
    'anc.areaname',
    'lc.loan_category',
    'agc.agent_name',
    'lelc.loan_amnt',
    'lelc.maturity_date',
    'cs.in_closed_date'
);

$query = "SELECT 
    cp.id, lnc.linename, lelc.loan_id, lelc.loan_date, cp.aadhar_num, cp.cus_id, cp.cus_name, anc.areaname, lc.loan_category, agc.agent_name, 
    lelc.loan_amnt, lelc.maturity_date, cs.in_closed_date
FROM 
    customer_profile cp 
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
    LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
WHERE cs.in_closed_date IS NOT NULL AND lelc.due_type = 'Interest' $where ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lnc.linename LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_id LIKE '%" . $_POST['search'] . "%' OR
            cp.aadhar_num LIKE '%" . $_POST['search'] . "%' OR
            cp.cus_id LIKE '%" . $_POST['search'] . "%' OR
            cp.cus_name LIKE '%" . $_POST['search'] . "%' OR
            anc.areaname LIKE '%" . $_POST['search'] . "%' OR
            lc.loan_category LIKE '%" . $_POST['search'] . "%' OR
            agc.agent_name LIKE '%" . $_POST['search'] . "%' OR
            lelc.loan_amnt LIKE '%" . $_POST['search'] . "%' ) ";
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

if ($_POST['length'] != -1) {
    $statement = $pdo->prepare($query . $query1);
    $statement->execute();
}
$result = $statement->fetchAll();

$data = array();
$sno = 1;
foreach ($result as $row) {
    $sub_array   = array();
    $sub_array[] = $sno;
    $sub_array[] = $row['linename'];
    $sub_array[] = $row['loan_id'];
    $sub_array[] = !empty($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['loan_amnt'];
    $sub_array[] = date('d-m-Y', strtotime($row['maturity_date']));
    $sub_array[] = !empty($row['in_closed_date']) ? date('d-m-Y', strtotime($row['in_closed_date'])) : '';

    $data[]      = $sub_array;
    $sno++;
}

function count_all_data($pdo)
{
    $query = $pdo->query("SELECT count(cs.cus_profile_id) as cus_profile_id FROM customer_status cs LEFT JOIN loan_entry_loan_calculation lelc ON cs.cus_profile_id = lelc.cus_profile_id where lelc.due_type = 'Interest' ");
    $statement = $query->fetch();
    return $statement['cus_profile_id'];
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

// Close the database pdoion
$pdo = null;
