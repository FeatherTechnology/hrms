<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$status = [2 => 'aa', 3 =>'Move',4 => 'Approved',5 => 'Cancel',6 => 'Revoke',7 => 'Current',8 => 'In Closed',9=>'Closed',10=>'NOC', 11 => 'NOC Completed', 12 => 'NOC Removed',13 => 'Cancel',14 => 'Revoke'];
$sub_status = [''=>'',1=>'Consider',2=>'Reject'];

$column = array(
    'c.id',
    'lnc.linename',
    'lelc.loan_id',
    'li.issue_date',
    'c.cus_id',
    'cp.aadhar_num',
    'cp.cus_name',
    'anc.areaname',
    'bc.branch_name',
    'cp.mobile1',
    'lc.loan_category',
    'agc.agent_name',
    'r.role',
    'u.name',
    'c.coll_date',
    'SUM(c.due_amt_track)',
    'SUM(c.princ_amt_track)',
    'SUM(c.int_amt_track)',
    'SUM(c.penalty_track)',
    'SUM(c.coll_charge_track)',
    'SUM(c.total_paid_track)',
    'cs.status',
    'cs.sub_status'
);

$query = "SELECT c.id, lnc.linename, lelc.loan_id, li.issue_date, c.cus_id, cp.aadhar_num, cp.cus_name, anc.areaname, bc.branch_name , cp.mobile1, lc.loan_category, agc.agent_name, r.role, u.name, c.trans_date, c.coll_date, lelc.due_type, lelc.due_period, lelc.principal_amnt, lelc.interest_amnt, SUM(c.due_amt_track) as due_amt_track, SUM(c.princ_amt_track) as princ_amt_track, SUM(c.int_amt_track) as int_amt_track, SUM(c.penalty_track) as penalty_track, SUM(c.coll_charge_track) as coll_charge_track, SUM(c.total_paid_track) as total_paid_track, cs.status, cs.sub_status 
FROM collection c
JOIN loan_issue li ON c.cus_profile_id = li.cus_profile_id 
JOIN customer_profile cp ON c.cus_profile_id = cp.id
JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
JOIN line_name_creation lnc ON cp.line = lnc.id
JOIN area_name_creation anc ON cp.area = anc.id
JOIN area_creation ac ON cp.line = ac.line_id
JOIN branch_creation bc ON ac.branch_id = bc.id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
JOIN customer_status cs ON cp.id = cs.cus_profile_id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
LEFT JOIN role r ON u.role = r.id
JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
WHERE li.balance_amount = 0 AND lcc.due_type = 'emi' AND u.id ='$user_id' AND us.id ='$user_id' AND DATE(c.coll_date) BETWEEN '$from_date' AND '$to_date' ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lelc.loan_id LIKE '%" . $_POST['search'] . "%'
                    OR li.issue_date LIKE '%" . $_POST['search'] . "%'
                    OR c.cus_id LIKE '%" . $_POST['search'] . "%'
                    OR cp.aadhar_num LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_name LIKE '%" . $_POST['search'] . "%'
                    OR lnc.linename LIKE '%" . $_POST['search'] . "%'
                    OR anc.areaname LIKE '%" . $_POST['search'] . "%'
                    OR bc.branch_name LIKE '%" . $_POST['search'] . "%'
                    OR r.role LIKE '%" . $_POST['search'] . "%'
                    OR u.name LIKE '%" . $_POST['search'] . "%'
                    OR c.coll_date LIKE '%" . $_POST['search'] . "%') ";
    }
}

$query .= " GROUP BY lelc.loan_id ";

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
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['role'];
    $sub_array[] = $row['name'];
    if ($row['trans_date'] != '0000-00-00') {
        $sub_array[] = date('d-m-Y', strtotime($row['trans_date']));
    } else {
        $sub_array[] = date('d-m-Y', strtotime($row['coll_date']));
    }

    if ($row['due_type'] != 'Interest') {
        //to get the principal and interest amt separate in due amt paid
        $response = calculatePrincipalAndInterest(intVal($row['principal_amnt']) / $row['due_period'], intVal($row['interest_amnt']) / $row['due_period'], intVal($row['due_amt_track']));

        $sub_array[] = moneyFormatIndia(intVal($row['due_amt_track']));
        $sub_array[] = moneyFormatIndia(intVal($response['principal_paid']));
        $rounderd_int = intVal($row['due_amt_track']) - $response['principal_paid'];
        $sub_array[] = moneyFormatIndia(intVal($rounderd_int));
    } else {
        //else if its interest loan we can empty due amt coz it will not be paid on that loan, direclty show princ and int
        $sub_array[] = '';
        $sub_array[] = moneyFormatIndia(intval($row['princ_amt_track']));
        $sub_array[] = moneyFormatIndia(intval($row['int_amt_track']));
    }
    $sub_array[] = moneyFormatIndia(intval($row['penalty_track']));
    $sub_array[] = moneyFormatIndia(intval($row['coll_charge_track']));
    $sub_array[] = moneyFormatIndia(intval($row['total_paid_track']));

    if ($row['status'] >= '8') {
        $sub_array[] = 'Closed';

        if ($row['sub_status'] != ''){
            $sub_array[] = $sub_status[$row['sub_status']];
        } else {
            $sub_array[] = $status[$row['status']];
        }
    } else {
        $sub_array[] = 'Present';
        $sub_array[] = $status[$row['status']];
    }

    $data[]      = $sub_array;
    $sno = $sno + 1;
}
function count_all_data($pdo)
{
    $query = "SELECT c.id FROM collection c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id WHERE lelc.due_type = 'EMI' GROUP BY c.cus_profile_id ";
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

function calculatePrincipalAndInterest($principal,  $interest,  $paidAmount): array
{
    $principal_paid = 0;
    $interest_paid = 0;

    while ($paidAmount > 0) {
        if ($paidAmount >= $principal) {
            $principal_paid += $principal;
            $paidAmount -= $principal;
        } else {
            $principal_paid += $paidAmount;
            break;
        }

        if ($paidAmount >= $interest) {
            $interest_paid += $interest;
            $paidAmount -= $interest;
        } else {
            $interest_paid += $paidAmount;
            break;
        }
    }

    return [
        'principal_paid' => (int) $principal_paid,
        'interest_paid' => (int) $interest_paid
    ];
}
