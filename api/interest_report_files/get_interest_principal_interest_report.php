<?php
include '../../ajaxconfig.php';

$where = "1";

if (isset($_POST['from_date']) && $_POST['from_date'] != '') {
    // Convert the input dates to month and year format
    $from_month = date('m', strtotime($_POST['from_date']));  // Extract month from from_date
    $from_year = date('Y', strtotime($_POST['from_date']));   // Extract year from from_date

    // Prepare WHERE condition to compare month and year
    $where  = "((YEAR(coll.coll_date) ='" . $from_year . "' AND MONTH(coll.coll_date) = '" . $from_month . "')) ";
}

$role_arr = [1 => 'Director', 2 => 'Agent', 3 => 'Staff'];

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
    'r.role',
    'u.name',
    'coll.coll_date',
    'cp.id',
    'cp.id',
    'SUM(coll.penalty_track)',
    'SUM(coll.coll_charge_track)',
    'SUM(coll.total_paid_track)'
);

$query = "SELECT  cp.id, lnc.linename, lelc.loan_id, lelc.loan_date, cp.aadhar_num, cp.cus_id, cp.cus_name, anc.areaname, lc.loan_category, agc.agent_name, r.role, u.name, 
coll.coll_date, SUM(coll.princ_amt_track) AS princ_amt_track, SUM(coll.int_amt_track) AS int_amt_track, SUM(coll.penalty_track) AS penalty_track, SUM(coll.coll_charge_track) AS coll_charge_track, SUM(coll.total_paid_track) AS total_paid_track ,  lelc.principal_amnt, lelc.interest_amnt, lelc.total_amnt
FROM 
    customer_profile cp
    LEFT JOIN collection coll ON cp.id = coll.cus_profile_id
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
    JOIN users u ON u.id = cs.update_login_id
    LEFT JOIN role r ON u.role = r.id
    LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
    WHERE cs.status >= 7 AND $where AND lelc.due_type = 'Interest' ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lelc.loan_id LIKE '%" . $_POST['search'] . "%'
                    OR lnc.linename LIKE '%" . $_POST['search'] . "%'
                    OR cp.aadhar_num LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_id LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_name LIKE '%" . $_POST['search'] . "%'
                    OR coll.cus_name LIKE '%" . $_POST['search'] . "%'
                    OR anc.areaname LIKE '%" . $_POST['search'] . "%'
                    OR lc.loan_category LIKE '%" . $_POST['search'] . "%'
                    OR agc.agent_name LIKE '%" . $_POST['search'] . "%'
                    OR r.role LIKE '%" . $_POST['search'] . "%'
                    OR u.name LIKE '%" . $_POST['search'] . "%'
                    OR coll.coll_date LIKE '%" . $_POST['search'] . "%') ";
    }
}

$query .= " GROUP BY coll.cus_profile_id ";


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
    $sub_array[] = date('d-m-Y', strtotime($row['loan_date']));
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['role'];
    $sub_array[] = $row['name'];
    $sub_array[] = date('d-m-Y', strtotime($row['coll_date']));
    $sub_array[] = moneyFormatIndia(intval($row['princ_amt_track']));
    $sub_array[] = moneyFormatIndia(intval($row['int_amt_track']));
    $sub_array[] = moneyFormatIndia(intval($row['penalty_track']));
    $sub_array[] = moneyFormatIndia(intval($row['coll_charge_track']));
    $sub_array[] = moneyFormatIndia(intval($row['total_paid_track']));
    $data[]      = $sub_array;
    $sno = $sno + 1;
}
function count_all_data($pdo)
{
    $query = $pdo->query("SELECT COUNT(subquery.id) AS count_result FROM ( SELECT coll.id FROM collection coll JOIN customer_status cs ON coll.cus_profile_id = cs.cus_profile_id LEFT JOIN loan_entry_loan_calculation lelc ON cs.cus_profile_id = lelc.cus_profile_id WHERE cs.status >= 7 AND lelc.due_type = 'Interest' GROUP BY coll.cus_profile_id ) AS subquery ");
    $statement = $query->fetch();
    return intVal($statement['count_result']);
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

// Close the database connection
$pdo = null;
