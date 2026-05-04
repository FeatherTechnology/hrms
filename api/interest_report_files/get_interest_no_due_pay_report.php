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
    'lelc.interest_amnt',
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
    lelc.interest_amnt,
    lelc.total_amnt,
    lelc.due_startdate,
    lelc.scheme_due_method,
    lelc.due_method,
    cs.status,
    col_sum.princ_amt_track,
    col_sum.int_amt_track , 
    lelc.interest_calculate , 
    lelc.interest_rate,
    cp.id as cus_profile_id,
    lelc.principal_amnt,
    IFNULL(col_sum.int_amt_track, 0) AS int_amt_track
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
            SUM(princ_amt_track) AS princ_amt_track, 
            SUM(int_amt_track) AS int_amt_track 
        FROM 
            collection
        WHERE 
            STR_TO_DATE(coll_date, '%Y-%m-%d') < '$full_date'
        GROUP BY 
            cus_profile_id
    ) col_sum ON col_sum.cus_profile_id = cp.id
WHERE
    cs.status >= 7 AND lelc.due_type = 'Interest' AND $where
    AND (
            CASE
                -- MONTHLY (interest_calculate = Month)
                WHEN (lelc.interest_calculate = 'Month') THEN
                    (
                        ((YEAR('$full_date') - YEAR(lelc.due_startdate)) * 12 + (MONTH('$full_date') - MONTH(lelc.due_startdate)) + 1)
                        * lelc.interest_amnt
                    )

                -- DAILY (interest_calculate = Days)
                WHEN lelc.interest_calculate = 'Days' THEN
                    (
                        (DATEDIFF('$full_date', lelc.due_startdate) + 1)
                        * lelc.interest_amnt
                    )
            END
            -
            IFNULL(col_sum.int_amt_track, 0)
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

        // ------------------------------------ MONTHLY LOGIC ------------------------------------ //

        if (($row['due_method'] == 'Monthly') || ($row['scheme_due_method'] == '1')) {

            $months = (date('Y', $end) - date('Y', $start)) * 12 + (date('m', $end) - date('m', $start)) + 1;
            $pending_month = $months - 1;
        }

        // ------------------------------------ DAILY LOGIC ------------------------------------ //

        else {
            $months = $start_date->diff($end_date)->days;
            $months += 1;
            $pending_month = $months - 1;
        }
    }

    // Balance Amount 
    if ($row['princ_amt_track'] != '') {
        $balance_amount = intval($row['principal_amnt']) - intval($row['princ_amt_track']);
    } else {
        $balance_amount = intval($row['principal_amnt']);
    }

    $cus_profile_id = $row['cus_profile_id'];

    // Prepare loan_arr and response for calculation
    $loan_arr = [
        'loan_date' => $row['loan_date'],
        'interest_calculate' => $row['interest_calculate'],
        'interest_rate' => $row['interest_rate']
    ];

    $response = [
        'interest_calculate' => $row['interest_calculate'],
        'interest_amount' => floatval($row['interest_amnt'])
    ];

    // Interest already paid
    $interest_paid = getPaidInterest($pdo, $cus_profile_id, $full_date);
    $pending_amount = pendingCalculation($pdo, $loan_arr, $response, $cus_profile_id, $full_date) - $interest_paid;
    $payable_amount = payableCalculation($pdo, $loan_arr, $response, $cus_profile_id, $full_date) - $interest_paid;

    if ($pending_amount < 0) {
        $pending_amount = 0;
    }

    if ($payable_amount < 0) {
        $payable_amount = 0;
    }

    $pending_amount = ceilAmount($pending_amount);
    $payable_amount = ceilAmount($payable_amount);

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
    $sub_array[] = $row['interest_amnt'];
    $sub_array[] = isset($payable_amount)  && $payable_amount > 0 ? moneyFormatIndia($payable_amount) : 0;
    $sub_array[] = 'Present';
    $payable_amount = max(0, $payable_amount);
    $pending_amount = max(0, $pending_amount);

    if ($payable_amount == 0  && $pending_amount == 0  && $balance_amount == 0) {
        $sub_array[] = 'Due Nil';
    } else if ($pending_amount == 0  &&  ((($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) >= date('Y-m', strtotime($full_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) >= strtotime($full_date))) && $balance_amount != 0) {
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
    if ($row['interest_amnt'] > 0) {
        $months_diff = (int) ceil($payable_amount / $row['interest_amnt']);
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
    WHERE lelc.due_type = 'Interest' AND cs.status >= 7 ");
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

function getPaidInterest($pdo, $cus_profile_id, $full_date)
{
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(int_amt_track), 0) + COALESCE(SUM(interest_waiver), 0) AS int_paid
        FROM collection 
        WHERE cus_profile_id = :cus_profile_id 
        AND (int_amt_track != '' and int_amt_track IS NOT NULL OR interest_waiver != '' and interest_waiver IS NOT NULL) AND  
        DATE(coll_date) <= DATE('$full_date')");

    $stmt->execute([':cus_profile_id' => $cus_profile_id]);

    $int_paid = $stmt->fetch(PDO::FETCH_ASSOC)['int_paid'] ?? 0;

    return floatval($int_paid);
}

function pendingCalculation($pdo, $loan_arr, $response, $cus_profile_id, $full_date)
{
    $pending = getTillDateInterest($loan_arr, $response, $pdo, 'pendingmonth', $cus_profile_id, $full_date);
    return $pending;
}

function getTillDateInterest($loan_arr, $response, $pdo, $data, $cus_profile_id, $full_date)
{

    if ($data == 'forstartmonth') {

        //get the loan isued month's date count
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

        // Use given full_date or fallback to today
        $cur_date = new DateTime($full_date ?? date('Y-m-d'));

        $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['interest_amount'], $loan_arr, '', $cus_profile_id);

        //to increase till date Interest to nearest multiple of 5
        $cur_amt = ceil($result / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
        if ($cur_amt < $result) {
            $cur_amt += 5;
        }
        $result = $cur_amt;
        return $result;
    }
    if ($data == 'curmonth') {


        // Use given full_date or fallback to today
        $cur_date = new DateTime($full_date ?? date('Y-m-d'));

        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));


        $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['interest_amount'], $loan_arr, 'TDI', $cus_profile_id);
        return $result;
    }
    if ($data == 'pendingmonth') {
        //for pending value check, goto 2 months before
        //bcoz last month value is on payable, till date int will be on cur date
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

        // Use given full_date or fallback to today
        $cur_date = new DateTime($full_date ?? date('Y-m-d'));

        $cur_date->modify('-2 months');
        $cur_date->modify('last day of this month');
        $result = 0;

        if ($issued_date->format('Y-m') <= $cur_date->format('Y-m')) {
            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['interest_amount'], $loan_arr, 'pending', $cus_profile_id);
        }
        return $result;
    }

    return $response;
}

function payableCalculation($pdo, $loan_arr, $response, $cus_profile_id, $full_date)
{
    $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
    // Use given full_date or fallback to today
    $cur_date = new DateTime($full_date ?? date('Y-m-d'));
    $result = 0;

    if ($response['interest_calculate'] == "Month") {
        $last_month = clone $cur_date;
        $last_month->modify('-1 month'); // Last month same date
        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_month->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date; // Due to mutation in function

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['interest_amount'], $loan_arr, 'payable', $cus_profile_id);

            $st_date->modify('+1 month');
            $st_date->modify('first day of this month');
        }
    } elseif ($response['interest_calculate'] == "Days") {
        $last_date = clone $cur_date;
        $last_date->modify('-1 month'); // Last month same date
        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_date->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date;

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['interest_amount'], $loan_arr, 'payable', $cus_profile_id);
            $st_date->modify('+1 month');
            $st_date->modify('first day of this month');
        }
    }

    return $result;
}

function dueAmtCalculation($pdo, $start_date, $end_date, $interest_amount, $loan_arr, $status, $cus_profile_id)
{
    $start = new DateTime($start_date->format('Y-m-d'));
    $end = new DateTime($end_date->format('Y-m-d'));

    if (isset($_POST['full_date']) && $_POST['full_date'] != '') {
        $full_date = date('Y-m-d', strtotime($_POST['full_date']));
    } else {
        $full_date = date('Y-m-d');
    }

    $interest_calculate = $loan_arr['interest_calculate'];
    $int_rate = $loan_arr['interest_rate'];
    $result = 0;
    $monthly_interest_data = [];

    $loanRow = $pdo->query("SELECT loan_amnt FROM loan_entry_loan_calculation WHERE cus_profile_id = '" . $cus_profile_id . "'")->fetch(PDO::FETCH_ASSOC);
    $default_balance = $loanRow['loan_amnt'];

    $collections = $pdo->query("SELECT princ_amt_track,principal_waiver, coll_date FROM collection 
        WHERE cus_profile_id = '" . $cus_profile_id . "' AND (princ_amt_track != '' OR principal_waiver != '') AND DATE(coll_date) <= DATE('$full_date') ORDER BY coll_date ASC")->fetchAll();

    if (!empty($collections)) {

        // <---------------------------------------------------------------- IF COLLECTIONS EXIST ------------------------------------------------------------>

        $collection_index = 0;
        $current_balance = $default_balance;

        while ($start <= $end) {
            $today_str = $start->format('Y-m-d');
            $month_key = $start->format('Y-m-01');
            $paid_principal_today = 0;
            $paid_principal_waiver = 0;

            while ($collection_index < count($collections)) {
                $collection = $collections[$collection_index];
                $coll_date = (new DateTime($collection['coll_date']))->format('Y-m-d');
                if ($coll_date == $today_str) {
                    $paid_principal_today += (float)$collection['princ_amt_track'];
                    $paid_principal_waiver += (float)$collection['principal_waiver'];
                    $collection_index++;
                } else {
                    break;
                }
            }

            $current_balance = max(0, $current_balance - ($paid_principal_today + $paid_principal_waiver));

            $interest_today = calculateNewInterestAmt($int_rate, $current_balance, $interest_calculate);

            if ($interest_calculate === 'Days') {
                $result += $interest_today;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $interest_today;
            } else {
                $days_in_month = (int)$start->format('t');
                $daily_interest = $interest_today / $days_in_month;
                $result += $daily_interest;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $daily_interest;
            }

            $start->modify('+1 day');
        }
    } else {
        $monthly_interest_data = [];

        if ($interest_calculate == 'Month') {
            while ($start->format('Y-m') <= $end->format('Y-m')) {
                $month_key = $start->format('Y-m-d');
                $dueperday = $interest_amount / intval($start->format('t'));

                if ($status != 'pending') {
                    if ($start->format('Y-m') != $end->format('Y-m')) {
                        $new_end_date = clone $start;
                        $new_end_date->modify('last day of this month');
                        $cur_result = (($start->diff($new_end_date))->days + 1) * $dueperday;
                    } else {
                        $cur_result = (($start->diff($end))->days + 1) * $dueperday;
                    }
                } else {
                    $new_end = clone $start;
                    $new_end->modify("last day of this month");
                    $cur_result = (($start->diff($new_end))->days + 1) * $dueperday;
                }

                $result += $cur_result;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $cur_result;
                $start->modify('+1 month');
                $start->modify('first day of this month');
            }
        } else if ($interest_calculate == 'Days') {
            while ($start->format('Y-m-d') <= $end->format('Y-m-d')) {
                $month_key = $start->format('Y-m-d');
                $dueperday = $interest_amount;
                $result += $dueperday;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $dueperday;

                $start->modify('+1 day');
            }
        }
    }
    return $result;
}

function calculateNewInterestAmt($interest_rate, $balance_amt, $interest_calculate)
{
    //to calculate current interest amount based on current balance_amt value//bcoz interest will be calculated based on current balance_amt amt only for interest loan
    if ($interest_calculate == 'Month') {
        $int = $balance_amt * ($interest_rate / 100);
    } else if ($interest_calculate == 'Days') {
        $int = ($balance_amt * ($interest_rate / 100) / 30);
    }

    $curInterest = ceil($int / 5) * 5; //to increase Interest to nearest multiple of 5
    if ($curInterest < $int) {
        $curInterest += 5;
    }
    $response = $curInterest;

    return $response;
}

function ceilAmount($amt)
{
    // Round the amount to avoid floating point precision errors.
    $amt = round($amt, 2);  // Round to two decimal places (or adjust as needed)
    $cur_amt = ceil($amt / 5) * 5;
    // If cur_amt is exactly equal to amt (with small floating point tolerance), don't increment.
    if (abs($cur_amt - $amt) < 0.01) {
        return $cur_amt;
    }
    if ($cur_amt < $amt) {
        $cur_amt += 5;
    }
    return $cur_amt;
}

// Close the database connection
$pdo = null;
