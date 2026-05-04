<?php
require "../../ajaxconfig.php";
@session_start();

$user_id   = $_SESSION['user_id'];
$from_date = $_POST['from_date'];
$to_date   = $_POST['to_date'];

$exp_cat = [
    "1" => 'Pooja',
    "2" => 'Vehicle',
    "3" => 'Fuel',
    "4" => 'Stationary',
    "5" => 'Press',
    "6" => 'Food',
    "7" => 'Rent',
    "8" => 'EB',
    "9" => 'Mobile bill',
    "10" => 'Office Maintenance',
    "11" => 'Salary',
    "12" => 'Tax & Auditor',
    "13" => 'Int Less',
    "14" => 'Agent Incentive',
    "15" => 'Common',
    "16" => 'Other'
];

$cash_type = ["1" => 'Hand Cash', "2" => 'Bank Cash'];

$column = array(
    'e.id',
    'e.created_on',
    'e.coll_mode',
    'b.bank_name',
    'e.invoice_id',
    'bc.branch_name',
    'e.expenses_category',
    'ac.agent_name',
    'e.total_issued',
    'e.total_amount',
    'e.description',
    'e.amount',
    'e.trans_id',
);

// Base SQL
$qry = "SELECT e.*, bc.branch_name, b.bank_name, ac.agent_name 
        FROM expenses e 
        LEFT JOIN branch_creation bc ON e.branch = bc.id 
        LEFT JOIN bank_creation b ON e.bank_id = b.id 
        LEFT JOIN agent_creation ac ON e.agent_id = ac.id 
        WHERE DATE(e.created_on) BETWEEN '$from_date' AND '$to_date'";

// Apply Search
if (isset($_POST['search']) && $_POST['search'] != "") {
    $search = trim($_POST['search']);
    $searchLower = strtolower($search);

    // Handle coll_mode mapping (case-insensitive)
    if ($searchLower == 'hand cash') {
        $qry .= " AND e.coll_mode = 1";
    } elseif ($searchLower == 'bank cash') {
        $qry .= " AND e.coll_mode = 2";
    } else {
        $qry .= " AND (
        e.invoice_id LIKE '%$search%'
        OR LOWER(b.bank_name) LIKE LOWER('%$search%')
        OR LOWER(bc.branch_name) LIKE LOWER('%$search%')
        OR LOWER(ac.agent_name) LIKE LOWER('%$search%')
        OR LOWER(e.description) LIKE LOWER('%$search%')
        OR e.trans_id LIKE '%$search%'
    ";

        // expense category mapping
        $catId = array_search(strtolower($search), array_map('strtolower', $exp_cat));
        if ($catId !== false) {
            $qry .= " OR e.expenses_category = '$catId'";
        }

        $qry .= ")";
    }
}

if (isset($_POST['order'])) {
    $qry .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $qry .= ' ';
}

$qry1 = "";
if ($_POST['length'] != -1) {
    $qry1 = " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
}

$statement = $pdo->prepare($qry);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($qry . $qry1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$sno = 1;

foreach ($result as $row) {
    $sub_array   = array();
    $sub_array[] = $sno;
    $sub_array[] = date('d-m-Y', strtotime($row['created_on']));
    $sub_array[] = isset($cash_type[$row['coll_mode']]) ? $cash_type[$row['coll_mode']] : $row['coll_mode'];
    $sub_array[] = $row['bank_name'];
    $sub_array[] = $row['invoice_id'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = isset($exp_cat[$row['expenses_category']]) ? $exp_cat[$row['expenses_category']] : $row['expenses_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['total_issued'];
    $sub_array[] = $row['total_amount'];
    $sub_array[] = $row['description'];
    $sub_array[] = moneyFormatIndia($row['amount']);
    $sub_array[] = $row['trans_id'];

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

function count_all_data($pdo)
{
    $query = "SELECT id FROM expenses";
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

//Format number in Indian Format
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
