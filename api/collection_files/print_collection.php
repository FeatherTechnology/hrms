<?php
require '../../ajaxconfig.php';
$coll_id = $_POST["coll_id"];

$qry = $pdo->query("SELECT * FROM `collection` WHERE coll_code='" . strip_tags($coll_id) . "'");
$row = $qry->fetch();

extract($row); // Extracts the array values into variables

$qry = $pdo->query("SELECT lelc.loan_id, lc.loan_category, lnc.linename ,lelc.due_type
FROM customer_profile cp 
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
WHERE cp.id = '$cus_profile_id'");
$row = $qry->fetch();
$line_name = $row['linename'];
$loan_category = $row['loan_category'];
$loan_id = $row['loan_id'];
$due_type = $row['due_type'];

$due_amt_track = intVal($due_amt_track != '' ? $due_amt_track : 0);
$penalty_track = intVal($penalty_track != '' ? $penalty_track : 0);
$princ_amt_track = intVal($princ_amt_track != '' ? $princ_amt_track : 0);
$int_amt_track = intVal($int_amt_track != '' ? $int_amt_track : 0);
$coll_charge_track = intVal($coll_charge_track != '' ? $coll_charge_track : 0);
$net_received = $due_amt_track + $penalty_track + $coll_charge_track + $princ_amt_track;
$due_balance = ($due_amt - $due_amt_track) < 0 ? 0 : $due_amt - $due_amt_track;
$interest_balance = ($due_amt - $int_amt_track) < 0 ? 0 : $due_amt - $int_amt_track;
$loan_balance = getBalance($pdo, $cus_profile_id, $coll_date);
?>


<div class="frame" id="dettable" style="position: relative; width: 302px; height: 500px; background-color: #ffffff;">
    <div class="overlap-group">
        <div class="captions" style="position: absolute; width: 112px; height: 278px; top: 150px; left: 45px;font-size: 12px">
            <!-- Other text-wrapper elements -->
            <b>
                <div class="text-wrapper" style="text-align:right;">Receipt No :</div>
            </b>
            <div class="div" style="text-align:right;">Date / Time :</div>
            <div class="text-wrapper-2" style="text-align:right;">Line / Area :</div>
            <div class="text-wrapper-3" style="text-align:right;">Customer ID :</div>
            <b>
                <div class="text-wrapper-4" style="text-align:right;">Customer Name :</div>
            </b>
            <div class="text-wrapper-6" style="text-align:right;">Loan Category :</div>
            <div class="text-wrapper-6" style="text-align:right;">Loan No :</div>
            <!-- Receipt Details -->
            <div class="text-wrapper-7" style="text-align:right;">
                <?php if ($due_type == 'EMI') { ?>
                    Due Receipt :
                <?php } else { ?>
                    Principal Receipt :
                <?php } ?>
            </div>

            <?php if ($due_type != 'EMI') { ?>
                <div class="text-wrapper-7" style="text-align:right;">Interest Receipt :</div>
            <?php } ?>
            <div class="text-wrapper-8" style="text-align:right;">Penalty :</div>
            <div class="text-wrapper-9" style="text-align:right;">Fine :</div><br>
            <b>
                <div class="text-wrapper-10" style="text-align:right;">Net Received :</div>
            </b><br>
            <div class="text-wrapper-11" style="text-align:right;">
                <?php if ($due_type == 'EMI') { ?>
                    Due Balance :
                <?php } else { ?>
                    Interest Balance :
                <?php } ?>
            </div>
            <div class="text-wrapper-12" style="text-align:right;">Loan Balance :</div>
        </div>
        <div class="data" style="position: absolute; width: 128px; height: 278px; top: 150px; left: 158px;font-size: 12px">
            <!-- Other text-wrapper elements -->
            <b>
                <div class="text-wrapper-13" style="margin-left: 5px;"><?php echo $coll_code; ?></div>
            </b>
            <div class="text-wrapper-14" style="margin-left: 5px;"><?php echo date('d-m-Y H:s A', strtotime($coll_date)); ?></div>
            <div class="text-wrapper-15" style="margin-left: 5px;"><?php echo $line_name; ?></div>
            <div class="text-wrapper-16" style="margin-left: 5px;"><?php echo $cus_id; ?></div>
            <b>
                <div class="text-wrapper-17" style="margin-left: 5px;"><?php echo $cus_name; ?></div>
            </b>
            <div class="text-wrapper-18" style="margin-left: 5px;"><?php echo $loan_category; ?></div>
            <div class="text-wrapper-19" style="margin-left: 5px;"><?php echo $loan_id; ?></div>
            <div class="text-wrapper-20" style="margin-left: 5px;">
                <?php if ($due_type == 'EMI') {
                    echo moneyFormatIndia($due_amt_track);
                } else {
                    echo moneyFormatIndia($princ_amt_track);
                } ?>
            </div>

            <?php if ($due_type != 'EMI') { ?>
                <div class="text-wrapper-20" style="margin-left: 5px;">
                    <?php echo moneyFormatIndia($int_amt_track); ?>
                </div>
            <?php } ?>
            <div class="text-wrapper-21" style="margin-left: 5px;"><?php echo moneyFormatIndia($penalty_track); ?></div>
            <div class="text-wrapper-22" style="margin-left: 5px;"><?php echo moneyFormatIndia($coll_charge_track); ?></div><br>
            <b>
                <div class="text-wrapper-23" style="margin-left: 5px;"><?php echo moneyFormatIndia($net_received); ?></div>
            </b><br>
            <div class="text-wrapper-24" style="margin-left: 5px;">
                <?php if ($due_type == 'EMI') {
                    echo moneyFormatIndia($due_balance);
                } else {
                    echo moneyFormatIndia($interest_balance);
                } ?>
            </div>
            <div class="text-wrapper-25" style="margin-left: 5px;"><?php echo moneyFormatIndia($loan_balance); ?></div>
        </div>
    </div>
    <img class="group" alt="Finance Software" src="img/fav.png" style="position: absolute; width: 150px; height: 91px; top: 34px; left: 44px;" />
</div>

<button type="button" name="printpurchase" onclick="poprint()" id="printpurchase" class="btn btn-primary">Print</button>

<script type="text/javascript">
    function poprint() {
        var Bill = document.getElementById("dettable").innerHTML;
        var printWindow = window.open('', '', 'height=1000;weight=1000;');
        printWindow.document.write('<html><head></head><body>');
        printWindow.document.write(Bill);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }
    setTimeout(() => {
        document.getElementById("printpurchase").click();

    }, 1500);
</script>

<?php
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

function getBalance($pdo, $cus_profile_id, $coll_date)
{
    $result = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE cus_profile_id = $cus_profile_id ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $loan_arr = $row;

        if ($loan_arr['total_amnt'] == '' || $loan_arr['total_amnt'] == null) {
            //(For monthly interest total amount will not be there, so take principals)
            $response['total_amt'] = intVal($loan_arr['principal_amnt']);
            $response['loan_type'] = 'interest';
            $loan_arr['loan_type'] = 'interest';
        } else {
            $response['total_amt'] = intVal($loan_arr['total_amnt']);
            $response['loan_type'] = 'emi';
            $loan_arr['loan_type'] = 'emi';
        }
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_profile_id ='" . $cus_profile_id . "' and date(coll_date) <= date('" . $coll_date . "') ");
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch()) {
            $coll_arr[] = $row;
        }
        $total_paid = 0;
        $total_paid_princ = 0;
        $total_paid_int = 0;
        $pre_closure = 0;
        $principal_waiver = 0;
        foreach ($coll_arr as $tot) {
            $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
            $total_paid_princ += intVal($tot['princ_amt_track']);
            $total_paid_int += intVal($tot['int_amt_track']);
            $pre_closure += intVal($tot['pre_close_waiver']); //get pre closure value to subract to get balance amount
            $principal_waiver += intVal($tot['principal_waiver']);
        }
        //total paid amount will be all records again request id should be summed
        $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
        $response['total_waiver'] = ($loan_arr['loan_type'] == 'emi') ? $pre_closure : $principal_waiver;
        $response['total_paid_int'] = $total_paid_int;
        $response['balance'] = $response['total_amt'] - $response['total_paid'] - $response['total_waiver'];
    } else {
        $response['balance'] = $response['total_amt'];
    }
    return $response['balance'];
}
?>