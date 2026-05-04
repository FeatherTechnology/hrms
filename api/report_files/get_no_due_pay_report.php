<?php
include '../../ajaxconfig.php';

if (isset($_POST['from_date']) && $_POST['from_date'] != '') {
    $full_date = $_POST['from_date'] . '-01';
    $from_month = date('m', strtotime($full_date));
    $from_year = date('Y', strtotime($full_date));
    $where = " NOT EXISTS(
        SELECT 1 
        FROM collection coll 
        WHERE coll.cus_profile_id = cs.cus_profile_id 
        AND YEAR(coll.coll_date) = '" . $from_year . "' 
        AND MONTH(coll.coll_date) = '" . $from_month . "'
    ) 
    AND lelc.due_startdate <= '$full_date' ";
}

$column = array(
    'cp.id',
    'lnc.linename',
    'lelc.loan_id',
    'lelc.loan_date',
    'lelc.maturity_date',
    'cp.aadhar_num',
    'cp.cus_id',
    'cp.cus_name',
    'anc.areaname',
    "lc.loan_category",
    'agc.agent_name',
    'r.role',
    'u.name',
    'lelc.due_amnt',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id'
);

$query = "SELECT lnc.linename,
    lelc.loan_id, 
    lelc.loan_date,  
    lelc.maturity_date, 
    cp.aadhar_num, 
    cp.cus_id, 
    cp.cus_name, 
    anc.areaname, 
    lc.loan_category, 
    agc.agent_name,
    r.role,
    u.name,
    lelc.due_amnt,
    lelc.total_amnt,
    lelc.due_startdate,
    lelc.scheme_due_method,
    lelc.due_method,
    cs.status,
    IFNULL(col_sum.total_due_amt_track, 0) AS total_due_amt
FROM
    customer_profile cp 
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
    LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
    JOIN users u ON u.id = cs.update_login_id
    LEFT JOIN role r ON u.role = r.id
    LEFT JOIN (
        SELECT 
            cus_profile_id, 
            SUM(due_amt_track) AS total_due_amt_track
        FROM 
            collection
        WHERE 
            STR_TO_DATE(coll_date, '%Y-%m-%d') < '$full_date'
        GROUP BY 
            cus_profile_id
    ) col_sum ON col_sum.cus_profile_id = cp.id
WHERE
    cs.status >= 7 AND lelc.due_type = 'EMI' AND $where
    AND (
            CASE 
                -- MONTHLY (due_method = Monthly OR scheme_due_method = 1)
                WHEN (lelc.due_method = 'Monthly' OR lelc.scheme_due_method = '1') THEN
                    (
                        ((YEAR('$full_date') - YEAR(lelc.due_startdate)) * 12 + (MONTH('$full_date') - MONTH(lelc.due_startdate)) + 1)
                        * lelc.due_amnt
                    )

                -- WEEKLY (scheme_due_method = 2)
                WHEN lelc.scheme_due_method = '2' THEN
                    (
                        (FLOOR(DATEDIFF('$full_date', lelc.due_startdate) / 7) + 1)
                        * lelc.due_amnt
                    )

                -- DAILY (scheme_due_method = 3)
                WHEN lelc.scheme_due_method = '3' THEN
                    (
                        (DATEDIFF('$full_date', lelc.due_startdate) + 1)
                        * lelc.due_amnt
                    )
            END
            -
            IFNULL(col_sum.total_due_amt_track, 0)
        ) > 0 ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lnc.linename LIKE '%" . $_POST['search'] . "%'
                    OR lelc.loan_id LIKE '%" . $_POST['search'] . "%'
                    OR lelc.loan_date LIKE '%" . $_POST['search'] . "%'
                    OR lelc.maturity_date LIKE '%" . $_POST['search'] . "%'
                    OR cp.aadhar_num LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_id LIKE '%" . $_POST['search'] . "%' 
                    OR anc.areaname LIKE '%" . $_POST['search'] . "%'
                    OR lc.loan_category LIKE '%" . $_POST['search'] . "%'
                    OR agc.agent_name LIKE '%" . $_POST['search'] . "%'
                    OR r.role LIKE '%" . $_POST['search'] . "%'
                    OR u.name LIKE '%" . $_POST['search'] . "%') ";
    }
}

$query .= " GROUP BY cp.id ";

if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
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
    if (strtotime($row['maturity_date']) < strtotime($full_date)) {

        $end   = strtotime($row['maturity_date']);
        $start = strtotime($row['due_startdate']);
        $search_date = strtotime($full_date);

        $start_date = new DateTime($row['due_startdate']);
        $end_date   = new DateTime($row['maturity_date']);

        $months = 0;
        $diff_days = 0;

        // ------------------------------------ MONTHLY LOGIC ------------------------------------ //

        if (($row['due_method'] == 'Monthly') || ($row['scheme_due_method'] == '1')) {

            $months = (date('Y', $end) - date('Y', $start)) * 12
                + (date('m', $end) - date('m', $start))
                + 1;

            if (
                date('m', $search_date) == date('m', $end) &&
                date('Y', $search_date) == date('Y', $end)
            ) {
                $months -= 1;
            }

            $pending_month = $months;
        }

        // ----------------------------------- WEEKLY LOGIC -------------------------------------- //

        else if (
            ($row['due_method'] != 'Monthly') &&
            ($row['scheme_due_method'] != '1') &&
            ($row['scheme_due_method'] != '3')
        ) {
            $diff_days  = $start_date->diff($end_date)->days;
            $months = ceil($diff_days / 7) + 1;
            $pending_month = $months;
        }

        // ------------------------------------ DAILY LOGIC ------------------------------------ //

        else {
            $months = $start_date->diff($end_date)->days + 1;
            $pending_month = $months;
        }
    } else {

        $end   = strtotime($full_date);
        $start = strtotime($row['due_startdate']);

        $start_date = new DateTime($row['due_startdate']);
        $end_date   = new DateTime($full_date);

        $months = 0;
        $diff_days = 0;

        // ------------------------------------ MONTHLY LOGIC ------------------------------------ //

        if (($row['due_method'] == 'Monthly') || ($row['scheme_due_method'] == '1')) {

            $months = (date('Y', $end) - date('Y', $start)) * 12 + (date('m', $end) - date('m', $start)) + 1;
            $pending_month = $months - 1;
        }

        // ----------------------------------- WEEKLY LOGIC -------------------------------------- //

        else if (
            ($row['due_method'] != 'Monthly') &&
            ($row['scheme_due_method'] != '1') &&
            ($row['scheme_due_method'] != '3')
        ) {
            $start = new DateTime($row['due_startdate']);
            $end   = new DateTime($full_date);

            // difference in days
            $diff_days = $start->diff($end)->days;

            // each week is a 7-day block
            // +1 because first week counts immediately (start_date <= end_date)
            $count = floor($diff_days / 7) + 1;

            $months = $count;
            $pending_month = max(0, $count - 1);
        }

        // ------------------------------------ DAILY LOGIC ------------------------------------ //

        else {
            $months = $start_date->diff($end_date)->days;
            $months += 1;
            $pending_month = $months - 1;
        }
    }

    $balance_amount = $row['total_amnt'] - $row['total_due_amt'];
    $payable_amount = ($months * $row['due_amnt']) - $row['total_due_amt'];
    $pending_amount = ($pending_month * $row['due_amnt']) - $row['total_due_amt'];

    $sub_array   = array();
    $sub_array[] = $sno;
    $sub_array[] = $row['linename'];
    $sub_array[] = $row['loan_id'];
    $sub_array[] = date('d-m-Y', strtotime($row['loan_date']));
    $sub_array[] = date('d-m-Y', strtotime($row['maturity_date']));
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['role'];
    $sub_array[] = $row['name'];
    $sub_array[] = $row['due_amnt'];
    $sub_array[] = isset($payable_amount)  && $payable_amount > 0 ? moneyFormatIndia($payable_amount) : 0;
    $sub_array[] = 'Present';
    $payable_amount = max(0, $payable_amount);
    $pending_amount = max(0, $pending_amount);

    if ($payable_amount == 0  && $pending_amount == 0  && $balance_amount == 0) {
        $sub_array[] = 'Due Nil';
    } else if ($payable_amount <= $row['due_amnt'] && $pending_amount == 0  &&  ((($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) >= date('Y-m', strtotime($full_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) >= strtotime($full_date))) && $balance_amount != 0) {
        $sub_array[] = 'Current';
    } else if ($pending_amount > 0 &&  (
        (($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) >= date('Y-m', strtotime($full_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) > strtotime($full_date))
    )) {
        $sub_array[] = 'Pending';
    } else if (
        (
            ($balance_amount  > 0) && ((($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) < date('Y-m', strtotime($full_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) < strtotime($full_date)))
        )
    ) {
        $sub_array[] = 'OD';
    } else {
        $sub_array[] = 'No Result';
    }

    // month categorization logic
    $months_diff = 0;
    if ($row['due_amnt'] > 0) {
        $months_diff = (int) ceil($payable_amount / $row['due_amnt']);
    }
    $monthCols = [0, 0, 0, 0, 0, 0]; // one to above_five
    if ($months_diff >= 6) {
        $monthCols[5] = 1;
    } elseif ($months_diff == 5) {
        $monthCols[4] = 1;
    } elseif ($months_diff == 4) {
        $monthCols[3] = 1;
    } elseif ($months_diff == 3) {
        $monthCols[2] = 1;
    } elseif ($months_diff == 2) {
        $monthCols[1] = 1;
    } elseif ($months_diff == 1) {
        $monthCols[0] = 1;
    }

    foreach ($monthCols as $mc) {
        $sub_array[] = $mc;
    }

    $sub_array[] = moneyFormatIndia(!empty($balance_amount) ? $balance_amount : 0);

    $data[]      = $sub_array;
    $sno = $sno + 1;
}

function count_all_data($pdo)
{
    $query = $pdo->query(" SELECT COUNT(*) AS count_result FROM loan_entry_loan_calculation lelc LEFT JOIN customer_status cs ON lelc.cus_profile_id = cs.cus_profile_id
    WHERE lelc.due_type = 'EMI' AND cs.status >= 7 ");
    $statement = $query->fetch();
    return intval($statement['count_result']);
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
