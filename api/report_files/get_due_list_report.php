<?php
include '../../ajaxconfig.php';

if (isset($_POST['to_date']) && $_POST['to_date'] != '') {
    $to_date = date('Y-m-d', strtotime($_POST['to_date']));
} else {
    $to_date = date('Y-m-d');
}

$column = array(
    'cp.id',
    'lnc.linename',
    'lelc.loan_id',
    'lelc.loan_date',
    'lelc.due_startdate',
    'lelc.maturity_date',
    'cp.aadhar_num',
    'cp.cus_id',
    'cp.cus_name',
    'cp.mobile1',
    'anc.areaname',
    'lc.loan_category',
    'agc.agent_name',
    'fi.fam_name',
    'fi.fam_relationship',
    'fi.fam_mobile',
    'lelc.loan_amnt',
    'lelc.due_amnt',
    'lelc.due_period',
    'lelc.total_amnt',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id',
    'cp.id'
);

$qry = " SELECT lelc.cus_profile_id FROM loan_entry_loan_calculation lelc
LEFT JOIN customer_status cs ON lelc.cus_profile_id = cs.cus_profile_id
LEFT JOIN collection c ON c.cus_profile_id = lelc.cus_profile_id
LEFT JOIN (
    SELECT cus_profile_id, MAX(coll_date) AS last_collection_date
    FROM collection
    GROUP BY cus_profile_id
) sub ON c.cus_profile_id = sub.cus_profile_id
WHERE lelc.due_startdate <= '$to_date' AND (cs.status = 7
AND (cs.coll_status != 'Due Nil' OR (cs.coll_status = 'Due Nil' AND sub.last_collection_date > '$to_date'))) OR (cs.status > 7 AND cs.in_closed_date >= '$to_date')";

$run = $pdo->query($qry);
$cus_profile_id_list = [];

while ($row = $run->fetch()) {
    $cus_profile_id_list[] = $row['cus_profile_id'];
}

if (!empty($cus_profile_id_list)) {
    // Convert array to comma separated list
    $cus_profile_id_list = implode(',', $cus_profile_id_list);
} else {
    // No values → fallback to 0
    $cus_profile_id_list = 0;
}

$query = "SELECT lnc.linename, 
lelc.loan_id, 
lelc.loan_date, 
lelc.due_startdate, 
lelc.maturity_date, 
cp.aadhar_num, 
cp.cus_id, 
cp.cus_name, 
cp.mobile1, 
anc.areaname, 
lc.loan_category, 
agc.agent_name, 
fi.fam_name, 
fi.fam_relationship, 
fi.fam_mobile, 
lelc.loan_amnt, 
lelc.due_amnt, 
lelc.due_period, 
lelc.total_amnt,
lelc.due_method,
lelc.scheme_due_method,
cs.status,
IFNULL(NULLIF(c.pending, ''), 0) AS pending, 
IFNULL(NULLIF(c.payable_amt, ''), 0) AS payable_amt,
IFNULL(NULLIF(c.total_paid_track, ''), 0) AS total_paid_track, 
IFNULL(NULLIF(c.due_amt_track, ''), 0) AS due_amt_track,
IFNULL(NULLIF(c.total_due_amt, ''), 0) AS total_due_amt, 
IFNULL(NULLIF(c.bal_amt, ''), lelc.total_amnt) AS bal_amt, 
IFNULL(NULLIF(coll_id, ''), 0) AS coll_id
FROM
    customer_profile cp 
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
    LEFT JOIN agent_creation agc ON lelc.agent_id = agc.id
    LEFT JOIN family_info fi ON cp.guarantor_name = fi.id
    LEFT JOIN (
        SELECT 
            c1.cus_profile_id,
            c1.pending_amt AS pending,
            c1.payable_amt,
            c1.total_paid_track,
            c1.due_amt_track,
            c1.bal_amt,
            c1.id AS coll_id,

            (
                SELECT SUM(c2.due_amt_track)
                FROM collection c2
                WHERE c2.cus_profile_id = c1.cus_profile_id
                AND DATE(c2.coll_date) < '$to_date'
            ) AS total_due_amt

        FROM collection c1

        INNER JOIN (
            SELECT cus_profile_id, MAX(id) AS max_coll_id
            FROM collection
            WHERE cus_profile_id IN ($cus_profile_id_list)
            AND DATE(coll_date) < '$to_date'
            GROUP BY cus_profile_id
        ) AS last_rec
        ON c1.cus_profile_id = last_rec.cus_profile_id
        AND c1.id = last_rec.max_coll_id

    ) AS c
    ON cp.id = c.cus_profile_id

WHERE
    cp.id IN ($cus_profile_id_list) AND lelc.due_type = 'EMI' ";

if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $query .= " and (lnc.linename LIKE '%" . $_POST['search'] . "%'
                    OR lelc.loan_id LIKE '%" . $_POST['search'] . "%'
                    OR lelc.loan_date LIKE '%" . $_POST['search'] . "%'
                    OR cp.aadhar_num LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_id LIKE '%" . $_POST['search'] . "%'
                    OR cp.cus_name LIKE '%" . $_POST['search'] . "%'
                    OR cp.mobile1 LIKE '%" . $_POST['search'] . "%'
                    OR anc.areaname LIKE '%" . $_POST['search'] . "%'
                    OR lc.loan_category LIKE '%" . $_POST['search'] . "%'
                    OR agc.agent_name LIKE '%" . $_POST['search'] . "%'
                    OR fi.fam_name LIKE '%" . $_POST['search'] . "%'
                    OR fi.fam_relationship LIKE '%" . $_POST['search'] . "%'
                    OR fi.fam_mobile LIKE '%" . $_POST['search'] . "%'
                    OR lelc.loan_amnt LIKE '%" . $_POST['search'] . "%') ";
    }
}

if (isset($_POST['order'])) {
    $col = $column[$_POST['order'][0]['column']];
    $dir = $_POST['order'][0]['dir'];
    $query .= " ORDER BY CAST($col AS UNSIGNED) $dir ";
} else {
    $query .= " ";
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
    if (strtotime($row['maturity_date']) < strtotime($to_date)) {

        $end   = strtotime($row['maturity_date']);
        $start = strtotime($row['due_startdate']);
        $search_date = strtotime($to_date);

        $start_date = new DateTime($row['due_startdate']);
        $to_dt      = new DateTime($to_date);
        $maturity_dt = new DateTime($row['maturity_date']);

        $months = 0;
        $diff_days = 0;
        $diff_weeks = 0;
        $od_months = 0;

        // OD days from maturity → to_date
        $od_diff_days = $maturity_dt->diff($to_dt)->days;

        // ------------------------------------ MONTHLY LOGIC ------------------------------------ //

        if (($row['due_method'] == 'Monthly') || ($row['scheme_due_method'] == '1')) {

            // PENDING
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

            // ---------------- MONTHLY OD (DECIMAL MONTHS) ---------------- //
            $days_in_maturity_month = (int)$maturity_dt->format('t');
            $od_months = round($od_diff_days / $days_in_maturity_month, 2);
        }

        // ----------------------------------- WEEKLY LOGIC -------------------------------------- //

        else if (
            ($row['due_method'] != 'Monthly') &&
            ($row['scheme_due_method'] != '1') &&
            ($row['scheme_due_method'] != '3')
        ) {

            // PENDING 
            $diff_days  = $start_date->diff($maturity_dt)->days;
            $months = ceil($diff_days / 7) + 1;
            $pending_month = $months;

            // WEEKLY OD (decimal weeks)
            $od_months = round($od_diff_days / 7, 2);
        }

        // ------------------------------------ DAILY LOGIC ------------------------------------ //

        else {

            // PENDING 
            $months = $start_date->diff($maturity_dt)->days + 1;
            $pending_month = $months;

            // DAILY OD (days)
            $od_months = $od_diff_days;
        }
    } else {

        $end   = strtotime($to_date);
        $start = strtotime($row['due_startdate']);

        $start_date = new DateTime($row['due_startdate']);
        $end_date   = new DateTime($to_date);

        $months = 0;
        $diff_days = 0;
        $diff_weeks = 0;

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
            $end   = new DateTime($to_date);

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

    $row['total_amnt']     = floatval($row['total_amnt']);
    $row['total_due_amt']  = floatval($row['total_due_amt']);
    $row['due_amnt']    = floatval($row['due_amnt']);
    $row['due_period']     = floatval($row['due_period']);

    $balance_amount = $row['total_amnt'] - $row['total_due_amt'];
    $paid_due = $row['total_due_amt'] / $row['due_amnt'];
    $balance_due = (float)$row['due_period'] - $paid_due;
    $pending_amount = ($pending_month * $row['due_amnt']) - $row['total_due_amt'];
    $payable_amount = ($months * $row['due_amnt']) - $row['total_due_amt'];
    $pending_due = max(0, $pending_amount / $row['due_amnt']);

    $sub_array   = array();
    $sub_array[] = $sno;
    $sub_array[] = $row['linename'];
    $sub_array[] = $row['loan_id'];
    $sub_array[] = date('d-m-Y', strtotime($row['loan_date']));
    $sub_array[] = date('d-m-Y', strtotime($row['due_startdate']));
    $sub_array[] = date('d-m-Y', strtotime($row['maturity_date']));
    $sub_array[] = $row['aadhar_num'];
    $sub_array[] = $row['cus_id'];
    $sub_array[] = $row['cus_name'];
    $sub_array[] = $row['mobile1'];
    $sub_array[] = $row['areaname'];
    $sub_array[] = $row['loan_category'];
    $sub_array[] = $row['agent_name'];
    $sub_array[] = $row['fam_name'];
    $sub_array[] = $row['fam_relationship'];
    $sub_array[] = $row['fam_mobile'];
    $sub_array[] = moneyFormatIndia($row['loan_amnt']);
    $sub_array[] = moneyFormatIndia($row['due_amnt']);
    $sub_array[] = $row['due_period'];
    $sub_array[] = moneyFormatIndia($row['total_amnt']);
    $sub_array[] = isset($balance_amount) && $balance_amount >= 0 ? moneyFormatIndia($balance_amount) : $row['total_amnt'];
    $sub_array[] = isset($balance_due) && $balance_due >= 0 ? number_format($balance_due, 1, '.', '') : 0;
    $sub_array[] = isset($pending_amount) && ($pending_amount > 0) ? moneyFormatIndia($pending_amount) : 0;
    $sub_array[] = isset($pending_due) && $pending_due >= 0 ? number_format($pending_due, 1, '.', '') : 0;
    $sub_array[] = isset($od_months) ? ($od_months) : 0;
    $sub_array[] = isset($payable_amount) ? moneyFormatIndia($payable_amount) : 0;
    $sub_array[] = 'Present';
    $payable_amount = max(0, $payable_amount);
    $pending_amount = max(0, $pending_amount);

    if ($payable_amount == 0  && $pending_amount == 0  && $balance_amount == 0) {
        $sub_array[] = 'Due Nil';
    } else if ($payable_amount <= $row['due_amnt'] && $pending_amount == 0  &&  ((($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) >= date('Y-m', strtotime($to_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) >= strtotime($to_date))) && $balance_amount != 0) {
        $sub_array[] = 'Current';
    } else if ($pending_amount > 0 &&  (
        (($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) >= date('Y-m', strtotime($to_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) > strtotime($to_date))
    )) {
        $sub_array[] = 'Pending';
    } else if (
        (
            ($balance_amount  > 0) && ((($row['scheme_due_method'] === '1' || $row['due_method'] === 'Monthly') && date('Y-m', strtotime($row['maturity_date'])) < date('Y-m', strtotime($to_date))) || (($row['scheme_due_method'] != '1' || $row['due_method'] != 'Monthly') && strtotime($row['maturity_date']) < strtotime($to_date)))
        )
    ) {
        $sub_array[] = 'OD';
    } else {
        $sub_array[] = 'No Result';
    }


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
    $thecash = $thecash == 0 ? "0" : $thecash;
    return $thecash;
}

// Close the database pdoion
$pdo = null;
