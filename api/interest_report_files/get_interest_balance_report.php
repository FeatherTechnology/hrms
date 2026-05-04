<?php
include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$to_date = $_POST['to_date'];

$status = [2 => 'aa', 3 => 'Move', 4 => 'Approved', 5 => 'Cancel', 6 => 'Revoke', 7 => 'Current', 8 => 'In Closed', 9 => 'Closed', 10 => 'NOC', 11 => 'NOC Completed', 12 => 'NOC Removed'];

$column = [
    'li.id',
    'lnc.linename',
    'lelc.loan_id',
    'li.issue_date',
    'lelc.maturity_date',
    'cp.cus_id',
    'cp.aadhar_num',
    'cp.cus_name',
    'anc.areaname',
    'bc.branch_name',
    'cp.mobile1',
    'lc.loan_category',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
    'li.id',
];

$query = "SELECT li.id, li.cus_profile_id, lnc.linename, lelc.loan_id, li.issue_date, lelc.maturity_date, cp.cus_id, cp.aadhar_num, cp.cus_name, anc.areaname, bc.branch_name, cp.mobile1, lc.loan_category, ac.agent_name, lelc.loan_amnt, lelc.due_period, lelc.principal_amnt, lelc.interest_amnt,lelc.due_type, cs.status, c.princ_amt_track, c.int_amt_track , lelc.interest_calculate , lelc.interest_rate
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
        SUM(princ_amt_track) AS princ_amt_track, 
        SUM(int_amt_track) AS int_amt_track 
    FROM 
        collection 
    WHERE 
        DATE(coll_date) <= DATE('$to_date')
    GROUP BY 
        cus_profile_id
) c ON li.cus_profile_id = c.cus_profile_id
JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id
WHERE
    cs.status >= 7 AND DATE(li.issue_date) <= DATE('$to_date') AND lcc.due_type = 'interest'
    AND(cs.closed_date IS NULL OR DATE(cs.closed_date) >= DATE('$to_date'))
    AND ( IFNULL(c.princ_amt_track, 0) < lelc.loan_amnt
    OR (
        IFNULL(c.princ_amt_track, 0) = lelc.loan_amnt
        AND (
            DATE((
                SELECT MAX(col.coll_date)
                FROM collection col
                WHERE col.cus_profile_id = li.cus_profile_id
            )) > DATE('$to_date')
        )
    )
)";

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

    // Balance Amount 
    if ($row['princ_amt_track'] != '') {
        $balance_amt = intval($row['principal_amnt']) - intval($row['princ_amt_track']);
    } else {
        $balance_amt = intval($row['principal_amnt']);
    }

    $cus_profile_id = $row['cus_profile_id'];

    // Prepare loan_arr and response for calculation
    $loan_arr = [
        'loan_date' => $row['issue_date'],
        'interest_calculate' => $row['interest_calculate'],
        'interest_rate' => $row['interest_rate']
    ];

    $response = [
        'interest_calculate' => $row['interest_calculate'],
        'interest_amount' => floatval($row['interest_amnt'])
    ];


    // Pass the report date to override today in payableCalculation
    $payable_interest = payableCalculation($loan_arr, $response, $pdo, $cus_profile_id, $to_date);
    // Interest already paid
    $interest_paid = getPaidInterest($pdo, $cus_profile_id, $to_date);

    // Pending interest
    $pending_interest = ceilAmount($payable_interest) - $interest_paid;
    if ($pending_interest < 0) $pending_interest = 0;

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
    $sub_array[] = $row['due_period'];
    $sub_array[] = moneyFormatIndia($balance_amt);
    $sub_array[] = moneyFormatIndia($balance_amt);
    $sub_array[] = moneyFormatIndia($pending_interest);
    $sub_array[] = 'Present';
    $sub_array[] = $status[$row['status']];
    $data[] = $sub_array;
    $sno++;
}

function count_all_data($pdo)
{
    $query = "SELECT csts.id FROM customer_status csts LEFT JOIN loan_entry_loan_calculation lelc ON csts.cus_profile_id = lelc.cus_profile_id WHERE csts.status >= 7 AND csts.status <=8 AND lelc.due_type = 'Interest' ";
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

function payableCalculation($loan_arr, $response, $pdo, $cus_profile_id, $to_date = null)
{
    $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

    // Use given to_date or fallback to today
    $cur_date = new DateTime($to_date ?? date('Y-m-d'));

    $result = 0;

    if ($response['interest_calculate'] == "Month") {
        // Calculate till the last completed month before $to_date
        $last_month = clone $cur_date;
        $last_month->modify('first day of this month');
        $last_month->modify('-1 day'); // Last day of previous month

        $st_date = clone $issued_date;

        while ($st_date <= $last_month) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');

            // Prevent overshooting
            if ($end_date > $last_month) {
                $end_date = clone $last_month;
            }

            $start = clone $st_date;
            $result += dueAmtCalculation($pdo, $start, $end_date, $response['interest_amount'], $loan_arr, $cus_profile_id);

            $st_date->modify('first day of next month');
        }
    } elseif ($response['interest_calculate'] == "Days") {
        // Calculate till the last day of previous month
        $last_date = clone $cur_date;
        $last_date->modify('first day of this month');
        $last_date->modify('-1 day');

        $st_date = clone $issued_date;

        while ($st_date <= $last_date) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');

            // Prevent overshooting
            if ($end_date > $last_date) {
                $end_date = clone $last_date;
            }

            $start = clone $st_date;
            $result += dueAmtCalculation($pdo, $start, $end_date, $response['interest_amount'], $loan_arr, $cus_profile_id);

            $st_date->modify('first day of next month');
        }
    }

    return $result;
}

function dueAmtCalculation($pdo, $start_date, $end_date, $interest_amount, $loan_arr, $cus_profile_id)
{
    $start = new DateTime($start_date->format('Y-m-d'));
    $end = new DateTime($end_date->format('Y-m-d'));

    $interest_calculate = $loan_arr['interest_calculate'];
    $interest_rate = $loan_arr['interest_rate'];

    $result = 0;
    $monthly_interest_data = [];

    // Get default principal
    $loanRow = $pdo->query("SELECT loan_amnt FROM loan_entry_loan_calculation WHERE cus_profile_id = '$cus_profile_id'")->fetch(PDO::FETCH_ASSOC);
    $default_balance = (float)$loanRow['loan_amnt'];

    // Fetch collection entries
    $collections = $pdo->query("SELECT princ_amt_track, coll_date 
        FROM collection 
        WHERE cus_profile_id = '$cus_profile_id' AND princ_amt_track != '' 
        ORDER BY coll_date ASC")->fetchAll();

    // If collections exist, calculate dynamically
    if (!empty($collections)) {
        $collection_index = 0;
        $current_balance = $default_balance;

        while ($start <= $end) {
            $today_str = $start->format('Y-m-d');
            $month_key = $start->format('Y-m-01');
            $paid_principal_today = 0;

            // Apply principal payments made on this day
            while ($collection_index < count($collections)) {
                $collection = $collections[$collection_index];
                $coll_date = (new DateTime($collection['coll_date']))->format('Y-m-d');

                if ($coll_date == $today_str) {
                    $paid_principal_today += (float)$collection['princ_amt_track'];
                    $collection_index++;
                } else {
                    break;
                }
            }

            $current_balance -= $paid_principal_today;

            // Recalculate interest for the day
            $interest_today = calculateNewInterestAmt($interest_rate, $current_balance, $interest_calculate);

            if ($interest_calculate === 'Days') {
                $result += $interest_today;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $interest_today;
            } else {
                // For "Month" mode, distribute monthly interest daily
                $days_in_month = (int)$start->format('t');
                $daily_interest = $interest_today / $days_in_month;
                $result += $daily_interest;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $daily_interest;
            }

            $start->modify('+1 day');
        }
    } else {
        // No collections: Use flat logic
        if ($interest_calculate === 'Month') {
            while ($start <= $end) {
                $month_key = $start->format('Y-m-d');
                $days_in_month = (int)$start->format('t');
                $due_per_day = $interest_amount / $days_in_month;

                $period_end = clone $start;
                $period_end->modify('last day of this month');
                if ($period_end > $end) {
                    $period_end = clone $end;
                }

                $days = ($start->diff($period_end)->days) + 1;
                $cur_result = $due_per_day * $days;

                $result += $cur_result;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $cur_result;

                $start->modify('first day of next month');
            }
        } elseif ($interest_calculate === 'Days') {
            while ($start <= $end) {
                $month_key = $start->format('Y-m-d');
                $result += $interest_amount;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $interest_amount;
                $start->modify('+1 day');
            }
        }
    }

    return $result;
}

function getPaidInterest($pdo, $cus_profile_id, $to_date)
{
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(int_amt_track), 0) + COALESCE(SUM(interest_waiver), 0) AS int_paid
        FROM collection 
        WHERE cus_profile_id = :cus_profile_id 
        AND (int_amt_track != '' and int_amt_track IS NOT NULL OR interest_waiver != '' and interest_waiver IS NOT NULL) AND  
        DATE(coll_date) <= DATE('$to_date')");

    $stmt->execute([':cus_profile_id' => $cus_profile_id]);

    $int_paid = $stmt->fetch(PDO::FETCH_ASSOC)['int_paid'] ?? 0;

    return floatval($int_paid);
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
    $cur_amt = ceil($amt / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
    if ($cur_amt < $amt) {
        $cur_amt += 5;
    }
    return $cur_amt;
}
