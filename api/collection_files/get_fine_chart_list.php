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
<table class="table custom-table" id='collectionChargeListTable'>
    <thead>
        <tr>
            <th> S.No </th>
            <th> Date </th>
            <th> Fine </th>
            <th> Purpose </th>
            <th> Paid Date </th>
            <th> Paid Amount</th>
            <th> Balance Amount</th>
            <th> Waiver Amount</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $cp_id = $_POST['cp_id'];
        $run = $pdo->query("SELECT * FROM `collection_charges` WHERE `cus_profile_id`= '$cp_id' ");

        $i = 1;
        $charge = 0;
        $paid = 0;
        $waiver = 0;
        $bal_amnt = 0;
        while ($row = $run->fetch()) {
            $collCharges = ($row['coll_charge']) ? $row['coll_charge'] : '0';
            $charge = $charge + $collCharges; 
            $paidAmount = ($row['paid_amnt']) ? $row['paid_amnt'] : '0';
            $paid = $paid + $paidAmount;
            $waiverAmount = ($row['waiver_amnt']) ? $row['waiver_amnt'] : '0';
            $waiver = $waiver + $waiverAmount;
            $bal_amnt = $charge - $paid - $waiver;
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php if(isset($row['coll_date'])) echo date('d-m-Y',strtotime($row['coll_date'])); ?></td>
                <td><?php echo moneyFormatIndia($collCharges); ?></td>
                <td><?php echo $row['coll_purpose']; ?></td>
                <td><?php if(isset($row['paid_date'])) echo date('d-m-Y',strtotime($row['paid_date'])); ?></td>
                <td><?php echo moneyFormatIndia($paidAmount); ?></td>
                <td><?php echo moneyFormatIndia($bal_amnt); ?></td>
                <td><?php echo moneyFormatIndia($waiverAmount); ?></td>
            </tr>

        <?php $i++;
        } 
        $sumchargesAmnt = $pdo->query("SELECT sum(coll_charge) as charges,sum(paid_amnt) as paidAmnt,sum(waiver_amnt) as charges_waiver FROM `collection_charges` WHERE `cus_profile_id`= '$cp_id' ");
        $sumAmnt = $sumchargesAmnt->fetch();
        $charges = $sumAmnt['charges'];
        $paid_amt = $sumAmnt['paidAmnt'];
        $charges_waiver = $sumAmnt['charges_waiver'];
        ?>
    </tbody>
    <tr>
        <td></td>
        <td></td>
        <td><b><?php echo moneyFormatIndia($charges); ?></b></td>
        <td></td>
        <td></td>
        <td><b><?php echo moneyFormatIndia($paid_amt); ?></b></td>
        <td><b><?php echo moneyFormatIndia($bal_amnt); ?></b></td>
        <td><b><?php echo moneyFormatIndia($charges_waiver); ?></b></td>
    </tr>
</table>

<script type="text/javascript">
    $(function() {
        setdtable('#collectionChargeListTable',"Fine Chart List");
    });
</script>