<?php
include '../../ajaxconfig.php';
$inputDate = $_POST['toDate'];
$loan_category = $_POST['loan_category'];
$to_date = date('Y-m-d', strtotime($inputDate)) . ' 23:59:59';

?>

<style>
    table.custom-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    table.custom-table th {
        border-top: 1px solid white;
        border-left: 1px solid white;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
    }

    table.custom-table th[colspan] {
        text-align: center;
    }
</style>

<table id="interest_ledger_view_report" class="table custom-table">
    <thead>
        <tr>
            <th rowspan="2" style="width: 50px;">S.No</th>
            <th rowspan="2" style="width: 70px;">Cus ID</th>
            <th rowspan="2" style="width: 130px;">Customer Name</th>
            <th rowspan="2" style="width: 90px;">Loan ID</th>
            <th rowspan="2" style="width: 100px;">Loan Date</th>
            <th rowspan="2" style="width: 100px;">Maturity Date</th>
            <th rowspan="2" style="width: 130px;">Principal Balance</th>

            <?php
            $todate = new DateTime($to_date);
            $startDate = clone $todate;
            $startDate->modify('-11 months');
            $months = generateMonths($startDate, $todate);

            foreach ($months as $month) {
                echo '<th colspan="2" style="width: 160px;">' . date('M', strtotime($month)) . '</th>';
            }
            ?>
            <th colspan="2" style="width: 160px;">Paid Amount</th>
            <th rowspan="2" style="width: 150px;">Chart</th>
        </tr>
        <tr>
            <?php
            foreach ($months as $month) {
                echo '<th style="width: 80px;">Principal</th> <th style="width: 80px;">Interest</th>';
            }
            ?>
            <th style="width: 80px;">Principal</th>
            <th style="width: 80px;">Interest</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $query = "SELECT 
        li.cus_profile_id, 
        cp.cus_id, 
        cp.cus_name AS cus_name, 
        le.loan_id, 
        li.issue_date, 
        le.maturity_date, 
        c.princ_amt_track, 
        c.int_amt_track, 
        le.loan_amount,
        c.principal_waiver
    FROM loan_issue li
    JOIN loan_entry_loan_calculation le ON li.cus_profile_id = le.cus_profile_id
    JOIN customer_profile cp ON le.cus_profile_id = cp.id
    LEFT JOIN (
        SELECT 
            cus_profile_id, 
            SUM(princ_amt_track) AS princ_amt_track, 
            SUM(int_amt_track) AS int_amt_track,
            SUM(bal_amt) AS bal_amt,
            principal_waiver
        FROM collection 
        WHERE coll_date <= '$to_date'
        GROUP BY cus_profile_id
    ) c ON li.cus_profile_id = c.cus_profile_id
    WHERE 
    li.issue_date <= '$to_date'
    AND (
        (IFNULL(c.princ_amt_track, 0) != le.loan_amount)
        OR (
            (IFNULL(c.princ_amt_track, 0) = le.loan_amount)
            AND EXISTS (
                SELECT 1 FROM collection col 
                WHERE col.cus_profile_id = li.cus_profile_id 
                AND DATE(col.coll_date) = DATE('" . date('Y-m-d', strtotime($inputDate)) . "')
            )
        )
    )
    AND le.loan_category = '$loan_category' AND le.due_type = 'Interest'
    GROUP BY li.cus_profile_id 
    ORDER BY li.id ASC";

        $dailyData = $pdo->prepare($query);
        $dailyData->execute();
        $i = 1;
        $total_balance_amount = 0;
        $total_paid_principal = 0;
        $total_paid_interest = 0;

        while ($dailyInfo = $dailyData->fetch()) {
            // Reset row-level totals
            $row_total_principal = 0;
            $row_total_interest = 0;

        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $dailyInfo['cus_id']; ?></td>
                <td><?php echo $dailyInfo['cus_name']; ?></td>
                <td><?php echo $dailyInfo['loan_id']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['issue_date'])); ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['maturity_date'])); ?></td>
                <td>
                    <?php
                    $bal_amt = intval($dailyInfo['loan_amount']) - (intval($dailyInfo['princ_amt_track']) + intval($dailyInfo['principal_waiver']));
                    echo moneyFormatIndia($bal_amt);
                    ?>
                </td>
                <?php
                for ($j = 0; $j < count($months); $j++) {
                    $coll_qry = $pdo->query(" SELECT COALESCE(SUM(princ_amt_track), 0) AS princ_amt_track, 
                                COALESCE(SUM(int_amt_track), 0) AS int_amt_track 
                                FROM collection WHERE cus_profile_id = '" . $dailyInfo['cus_profile_id'] . "' 
                                AND MONTH(coll_date) = MONTH('" . $months[$j] . "') 
                                AND YEAR(coll_date) = YEAR('" . $months[$j] . "')
                                AND coll_date <= '$to_date'");

                    $coll_row = $coll_qry->fetch();

                    echo '<td>' . moneyFormatIndia($coll_row['princ_amt_track']) . '</td>';
                    echo '<td>' . moneyFormatIndia($coll_row['int_amt_track']) . '</td>';

                    // Row-wise totals
                    $row_total_principal += $coll_row['princ_amt_track'];
                    $row_total_interest += $coll_row['int_amt_track'];
                }

                // Print per-loan total paid principal & interest
                echo '<td>' . moneyFormatIndia($row_total_principal) . '</td>';
                echo '<td>' . moneyFormatIndia($row_total_interest) . '</td>';

                $total_paid_principal += $row_total_principal;
                $total_paid_interest += $row_total_interest;
                $total_balance_amount += $bal_amt;

                ?>
                <td>
                    <input type="button"
                        class="btn btn-primary due-chart"
                        value="Due Chart"
                        data-cusid="<?php echo $dailyInfo['cus_id']; ?>"
                        data-loanid="<?php echo $dailyInfo['cus_profile_id']; ?>">
                </td>

            </tr>
        <?php
        }
        ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="6"><b>Total</b></td>
            <td><b><?php echo moneyFormatIndia($total_balance_amount); ?></b></td>

            <?php
            // Leave empty cells for each month's principal and interest columns
            for ($j = 0; $j < count($months); $j++) {
                echo "<td></td><td></td>";
            }
            ?>

            <td><b><?php echo moneyFormatIndia($total_paid_principal); ?></b></td>
            <td><b><?php echo moneyFormatIndia($total_paid_interest); ?></b></td>
        </tr>
    </tfoot>

</table>

<?php

// Function to loop through months
function generateMonths($start, $end)
{
    $months = [];
    $currentDate = clone $start;

    while ($currentDate <= $end) {
        $months[] = $currentDate->format('Y-m-d');
        $currentDate->modify('+1 month');
    }

    return $months;
}

$pdo = null; // Close Connection

function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3);
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        foreach ($expunit as $i => $unit) {
            $explrestunits .= ($i == 0) ? (int)$unit . "," : $unit . ",";
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash;
}

?>

<script type="text/javascript">
    $(function() {
        setdtable('#interest_ledger_view_report',"Interest Ledger View List");
    });
</script>