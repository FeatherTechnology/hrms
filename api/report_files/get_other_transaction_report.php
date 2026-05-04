<?php
require "../../ajaxconfig.php";
@session_start();

$from_date = $_POST['from_date'];
$to_date   = $_POST['to_date'];
$sheet_type = $_POST['sheet_type'];

$trans_cat = [
    "1" => 'Deposit',
    "2" => 'Investment',
    "3" => 'EL',
    "4" => 'Exchange',
    "5" => 'Bank Deposit',
    "6" => 'Bank Withdrawal',
    "8" => 'Other Income',
    "9" => 'Bank Unbilled'
];

$cash_type = ["1" => 'Hand Cash', "2" => 'Bank Cash'];

$crdr = ["1" => 'Credit', "2" => 'Debit'];

$column = array(
    'a.id',
    'a.created_on',
    'a.coll_mode',
    'e.bank_name',
    'a.trans_cat',
    'a.name',
    'a.type',
    'a.ref_id',
    'a.trans_id',
    'a.amount',
    'a.remark'
);

$qry = "SELECT a.*, b.name AS transname, d.name as username, e.bank_name as bank_namecash 
    FROM `other_transaction` a 
    JOIN other_trans_name b ON a.name = b.id 
    LEFT JOIN users d ON a.user_name = d.id 
    LEFT JOIN bank_creation e ON a.bank_id = e.id 
    WHERE DATE(a.created_on) BETWEEN '$from_date' AND '$to_date' AND a.trans_cat = '$sheet_type'";

// Apply Search
if (isset($_POST['search']) && $_POST['search'] != "") {
    $search = trim($_POST['search']);
    $searchLower = strtolower($search);

    // Handle coll_mode mapping (case-insensitive)
    if ($searchLower == 'hand cash') {
        $qry .= " AND a.coll_mode = 1";
    } elseif ($searchLower == 'bank cash') {
        $qry .= " AND a.coll_mode = 2";
    } elseif ($searchLower == 'credit') {
        $qry .= " AND a.type = 1";
    } elseif ($searchLower == 'debit') {
        $qry .= " AND a.type = 2";
    } else {
        $qry .= " AND (
        LOWER(b.name) LIKE LOWER('%$search%')
        OR LOWER(d.name) LIKE LOWER('%$search%')
        OR LOWER(e.bank_name) LIKE LOWER('%$search%')
        OR a.ref_id LIKE '%$search%'
        OR a.trans_id LIKE '%$search%'
        OR a.remark LIKE '%$search%'
    ";

        // transaction category mapping
        $trans_catId = array_search(strtolower($search), array_map('strtolower', $trans_cat));
        if ($trans_catId !== false) {
            $qry .= " OR a.trans_cat = '$trans_catId'";
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
    $sub_array[] = $cash_type[$row['coll_mode']] ?? '';
    $sub_array[] = $row['bank_namecash'];
    $sub_array[] = $trans_cat[$row['trans_cat']] ?? '';
    $sub_array[] = $row['transname'];
    $sub_array[] = $crdr[$row['type']] ?? '';
    $sub_array[] = $row['ref_id'];
    $sub_array[] = $row['trans_id'];
    $sub_array[] = moneyFormatIndia($row['amount']);
    $sub_array[] = $row['remark'];

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

function count_all_data($pdo)
{
    $query = "SELECT id FROM other_transaction ";
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

// Format number in Indian format
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
