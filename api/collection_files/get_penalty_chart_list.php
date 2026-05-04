<?php
require '../../ajaxconfig.php';

function moneyFormatIndia($num)
{
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
    return $thecash;
}
?>
<table class="table custom-table" id='penaltyListTable'>
    <thead>
        <tr>
            <th width='20'> S.No </th>
            <th> Penalty Date </th>
            <th> Penalty </th>
            <th> Paid Date </th>
            <th> Paid Amount </th>
            <th> Balance Amount </th>
            <th> Waiver Amount </th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cp_id = $_POST['cp_id'];
        $cus_id = $_POST['cus_id'];
        $run = $pdo->query("SELECT * FROM `penalty_charges` WHERE `cus_profile_id`= '$cp_id' ORDER BY created_date ");

        $i = 1;
        $penalt = 0;
        $paid = 0;
        $waiver = 0;
        while ($row = $run->fetch()) {
            $penaltys = ($row['penalty']) ? $row['penalty'] : '0';
            $penalt = $penalt + $penaltys; 
            $paid_amount = ($row['paid_amnt']) ? $row['paid_amnt'] : '0';
            $paid = $paid + $paid_amount;
            $waivers = ($row['waiver_amnt']) ? $row['waiver_amnt'] : '0';
            $waiver = $waiver + $waivers ;
            $bal_amnt = $penalt - $paid - $waiver;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['penalty_date']!=''?date('d-m-Y',strtotime($row['penalty_date'])):''; ?></td>
                <td><?php echo moneyFormatIndia($penaltys); ?></td>
                <td><?php echo $row['paid_date']!=''?date('d-m-Y',strtotime($row['paid_date'])):''; ?></td>
                <td><?php echo moneyFormatIndia($paid_amount); ?></td>
                <td><?php echo moneyFormatIndia($bal_amnt); ?></td>
                <td><?php echo moneyFormatIndia($waivers); ?></td>
            </tr>

        <?php $i++;
        }

        $sumPenaltyAmnt = $pdo->query("SELECT sum(penalty) as penalte,sum(paid_amnt) as paidAmnt,sum(waiver_amnt) as penalte_waiver FROM `penalty_charges` WHERE `cus_profile_id`= '$cp_id' ");
        $sumAmnt = $sumPenaltyAmnt->fetch();
        $penalty = $sumAmnt['penalte'];
        $paid_amt = $sumAmnt['paidAmnt'];
        $penalty_waiver = $sumAmnt['penalte_waiver'];

        ?>

</tbody>
<tr>
    <td></td>
    <td></td>
    <td><b><?php echo moneyFormatIndia($penalty); ?></b></td>
    <td></td>
    <td><b><?php echo moneyFormatIndia($paid_amt); ?></b></td>
    <td></td>
    <td><b><?php echo moneyFormatIndia($penalty_waiver); ?></b></td>
</tr>
</table>

<script type="text/javascript">
    $(function() {
        setdtable('#penaltyListTable',"Penalty Chart List");
    });
</script>