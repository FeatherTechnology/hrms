<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$to_date = $_POST['to_date'];

$status = [2 => 'aa', 3 => 'Move', 4 => 'Approved', 5 => 'Cancel', 6 => 'Revoke', 7 => 'Current', 8 => 'In Closed', 9 => 'Closed', 10 => 'NOC', 11 => 'NOC Completed', 12 => 'NOC Removed'];

$column = [
    'li.id', 'lnc.linename', 'lelc.loan_id', 'li.issue_date', 'lelc.maturity_date',
    'cp.cus_id', 'cp.aadhar_num', 'cp.cus_name', 'anc.areaname', 'bc.branch_name', 'cp.mobile1',
    'lc.loan_category', 'li.id', 'li.id', 'li.id', 'li.id',
    'li.id', 'li.id', 'li.id', 'li.id', 'li.id',
    'li.id', 'li.id',
];

$query = "SELECT li.id, lnc.linename, lelc.loan_id, li.issue_date, lelc.maturity_date, cp.cus_id, cp.aadhar_num, cp.cus_name, anc.areaname, bc.branch_name, cp.mobile1, lc.loan_category, ac.agent_name, lelc.loan_amnt, lelc.due_amnt, lelc.due_period, lelc.total_amnt, lelc.principal_amnt, lelc.interest_amnt,lelc.due_type, cs.status, c.due_amt_track, c.princ_amt_track, c.int_amt_track
FROM loan_issue li  
JOIN customer_profile cp ON li.cus_profile_id = cp.id
JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id
JOIN line_name_creation lnc ON cp.line = lnc.id
JOIN area_name_creation anc ON cp.area = anc.id
JOIN branch_creation bc ON lnc.branch_id = bc.id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN agent_creation ac ON lelc.agent_id = ac.id
    LEFT JOIN (
        SELECT 
            cus_profile_id, 
            SUM(due_amt_track) AS due_amt_track, 
            SUM(princ_amt_track) AS princ_amt_track, 
            SUM(int_amt_track) AS int_amt_track 
        FROM 
            collection 
        GROUP BY 
            cus_profile_id
    ) c ON li.cus_profile_id = c.cus_profile_id
JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id
WHERE cs.status >=7 AND lcc.due_type = 'emi' AND cs.status <=8 AND date(li.issue_date) <= date('$to_date') ";

if (isset($_POST['search']) && $_POST['search'] != "") {
    $search = $_POST['search'];
    $query .= " AND (
        lnc.linename LIKE '%$search%' OR
        lelc.loan_id LIKE '%$search%' OR
        li.issue_date LIKE '%$search%' OR
        lelc.maturity_date LIKE '%$search%' OR
        cp.cus_id LIKE '%$search%' OR
        cp.aadhar_num LIKE '%$search%' OR
        cp.cus_name LIKE '%$search%' OR
        anc.areaname LIKE '%$search%' OR
        bc.branch_name LIKE '%$search%' OR
        cp.mobile1 LIKE '%$search%' OR
        lc.loan_category LIKE '%$search%'
    )";
}

$query .= "GROUP BY li.cus_profile_id";

$orderColumn = $_POST['order'][0]['column'] ?? null;
$orderDir = $_POST['order'][0]['dir'] ?? 'ASC';
if ($orderColumn !== null) {
    $query .= " ORDER BY " . $column[$orderColumn] . " " . $orderDir;
}

$statement = $pdo->prepare($query);
$statement->execute();
$number_filter_row = $statement->rowCount();

$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? -1;
if ($length != -1) {
    $query .= " LIMIT $start, $length";
}

$statement = $pdo->prepare($query);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$sno = 1;

foreach ($result as $row) {
    $sub_array = [];
    $balance_amt = ($row['due_type'] != 'Interest') ?
        intVal($row['total_amnt']) - intVal($row['due_amt_track']) :
        intVal($row['principal_amnt']) - intVal($row['princ_amt_track']);

    $princ_amt = intVal($row['principal_amnt']) / $row['due_period'];
    $int_amt = intVal($row['interest_amnt']) / $row['due_period'];
    $response = calculatePrincipalAndInterest($princ_amt, $int_amt, $balance_amt);

    if (intVal($response['principal_paid']) > intVal($row['loan_amnt'])) {
        $diff = intVal($response['principal_paid']) - intVal($row['loan_amnt']);
        $response['interest_paid'] += $diff;
        $response['principal_paid'] = intVal($row['loan_amnt']);
    }

    $bal_due = round($balance_amt / $row['due_amnt'], 1);

    $sub_array[] = $sno;
    $sub_array[] = $row['linename'];
    $sub_array[] = $row['loan_id'];
    $sub_array[] = date('d-m-Y', strtotime($row['issue_date']));
    $sub_array[] = date('d-m-Y', strtotime($row['maturity_date']));
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['mobile1'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = moneyFormatIndia($row['loan_amnt']);
    $sub_array[] = moneyFormatIndia($row['due_amnt']);
    $sub_array[] = $row['due_period'];
    $sub_array[] = moneyFormatIndia($row['total_amnt']);
    $sub_array[] = moneyFormatIndia($balance_amt);
    $sub_array[] = moneyFormatIndia($response['principal_paid']);
    $sub_array[] = moneyFormatIndia($response['interest_paid']);
    $sub_array[] = $bal_due;
    $sub_array[] = 'Present';
    $sub_array[] = $status[$row['status']];
    $data[] = $sub_array;
    $sno++;
}

function count_all_data($pdo)
{
    $query = "SELECT csts.id FROM customer_status csts LEFT JOIN loan_entry_loan_calculation lelc ON csts.cus_profile_id = lelc.cus_profile_id WHERE csts.status >= 7 AND csts.status <=8 AND lelc.due_type = 'EMI' ";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

$output = [
    'draw' => intval($_POST['draw']),
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data,
];

echo json_encode($output);

function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3);
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

function calculatePrincipalAndInterest($principal, $interest, $paidAmount)
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
        'principal_paid' => (int)$principal_paid,
        'interest_paid' => (int)$interest_paid,
    ];
}
