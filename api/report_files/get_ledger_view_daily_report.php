<?php
include '../../ajaxconfig.php';
$to_date = $_POST['toDate'];
?>

<table id="ledger_view_dailyreport_table" class="table custom-table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Cus ID</th>
            <th>Aadhar Number</th>
            <th>Loan ID</th>
            <th>Loan Date</th>
            <th>Maturity Date</th>
            <th>Balance Amount</th>
            <th>Sub Status</th>
            <?php
            $todate = new DateTime($to_date);
            $start = new DateTime($todate->format('Y-m-01'));
            $end = new DateTime($todate->format('Y-m-d'));
            $total_dates = 0;
            for ($date = $start; $date <= $end; $date->modify('+1 day')) {
                $total_dates++;
            ?>
                <th>
                    <?php echo $date->format('d'); ?>
                </th>
            <?php
            }
            ?>
            <th>Paid Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
     $query = "SELECT
     cp.id,
     cp.cus_id,
     cp.aadhar_num,
     lelc.loan_id,
     li.issue_date,
     lelc.maturity_date,
     c.coll_sub_status,
     COALESCE(
         (SELECT (bal_amt - due_amt_track)
          FROM collection
          WHERE cus_profile_id = cp.id
          ORDER BY id DESC
          LIMIT 1),
         loan_bal.total_bal_amt
     ) AS bal_amt
 FROM
     loan_issue li
 JOIN customer_profile cp ON
     li.cus_profile_id = cp.id
 JOIN loan_entry_loan_calculation lelc ON
     li.cus_profile_id = lelc.cus_profile_id
 LEFT JOIN(
     SELECT
         cus_profile_id,
         coll_sub_status
     FROM
         collection
     GROUP BY
         cus_profile_id
 ) c
 ON
     li.cus_profile_id = c.cus_profile_id
 JOIN customer_status cs ON
     li.cus_profile_id = cs.cus_profile_id
 -- Subquery to calculate the sum of cash, cheque_val, and transaction_val
 LEFT JOIN (
     SELECT
         cus_profile_id,
         COALESCE(SUM(cash) + SUM(cheque_val) + SUM(transaction_val), 0) AS total_bal_amt
     FROM
         loan_issue
     GROUP BY
         cus_profile_id
 ) loan_bal ON li.cus_profile_id = loan_bal.cus_profile_id
 WHERE
     COALESCE(
         (SELECT (bal_amt - due_amt_track)
          FROM collection
          WHERE cus_profile_id = cp.id
          ORDER BY id DESC
          LIMIT 1),
         loan_bal.total_bal_amt
     ) != 0
     AND lelc.profit_type = 1
     AND lelc.scheme_due_method = 3 AND lelc.due_type != 'interest'
     AND li.issue_date BETWEEN DATE_FORMAT('$to_date', '%Y-%m-01') AND '$to_date'
     GROUP BY li.cus_profile_id 
 ORDER BY
     li.id ASC; ";
  //loan type Scheme = 1 and daily loan =3. 
        $dailyData = $pdo->prepare($query);
        $dailyData->execute();
        $i = 1;
        $total_bal_sum = 0;
        $total_paid_sum = 0;
        while ($dailyInfo = $dailyData->fetch()) {
            $total_paid = 0;
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $dailyInfo['cus_id']; ?></td>
                <td><?php echo $dailyInfo['aadhar_num']; ?></td>
                <td><?php echo $dailyInfo['loan_id']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['issue_date'])); ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['maturity_date'])); ?></td>
                <td><?php echo moneyFormatIndia($dailyInfo['bal_amt']);
                    $bal_amnt = $dailyInfo['bal_amt']; ?></td>
                <td><?php echo ($dailyInfo['coll_sub_status']) ? $dailyInfo['coll_sub_status'] : 'Current'; ?></td>
                <?php
                $start = new DateTime($todate->format('Y-m-01'));
                $end = new DateTime($todate->format('Y-m-d'));
                $total_paid = 0;
                for ($date = $start; $date <= $end; $date->modify('+1 day')) {
                    $coll_qry = $pdo->query('SELECT SUM(due_amt_track) AS due_amt_track FROM collection WHERE cus_profile_id = ' . $dailyInfo['id'] . ' AND date(coll_date) = "' . date('Y-m-d', strtotime($date->format('Y-m-d'))) . '" ORDER BY id DESC ');
                    $due_amt_track = $coll_qry->fetch()['due_amt_track'] ?? 0;
                    $total_paid = $total_paid + $due_amt_track;
                ?>
                    <td><?php echo moneyFormatIndia($due_amt_track); ?></td>
                <?php
                }
                ?>
                <td><?php echo moneyFormatIndia($total_paid); ?></td>
            <?php
            $total_bal_sum += $bal_amnt;
            $total_paid_sum += $total_paid;
        }
            ?>

    </tbody>
    <tfoot>
        <?php
        $tfoot = "<tr><td colspan='6'><b>Total</b></td><td><b>" . moneyFormatIndia($total_bal_sum) . "</b></td><td></td><td colspan=" . $total_dates . "></td><td><b>" . moneyFormatIndia($total_paid_sum) . "</b></td></tr>";
        echo $tfoot;
        ?>
    </tfoot>
</table>

<?php
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
?>

<script type="text/javascript">
    $(function() {
        setdtable('#ledger_view_dailyreport_table',"Ledger View Daily List");
    });
</script>