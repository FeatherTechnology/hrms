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
<table class="table custom-table" id='dueChartListTable'>


    <?php
    $cp_id = $_POST['cp_id'];
    $cus_id = $_POST['cus_id'];
    $curDateChecker = true;
    if (isset($_POST['closed'])) {
        $closed = $_POST['closed'];
    } else {
        $closed = 'false';
    }
    $loanStart = $pdo->query("SELECT lelc.due_startdate, lelc.maturity_date, lelc.due_method, lelc.scheme_due_method,lelc.interest_calculate,lelc.interest_rate FROM loan_entry_loan_calculation lelc WHERE lelc.cus_profile_id = '$cp_id' ");
    $loanFrom = $loanStart->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    $due_start_from = $loanFrom['due_startdate'];
    $maturity_month = $loanFrom['maturity_date'];


    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
        //If Due method is Monthly, Calculate penalty by checking the month has ended or not

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 month');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $due_start_from = date('Y-m-d', strtotime($due_start_from));
        $maturity_month = date('Y-m-d', strtotime($maturity_month));
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
        $interval = new DateInterval('P1M'); // Create a one month interval
        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    } else
        if ($loanFrom['scheme_due_method'] == '2') {
        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-7 days');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    } else
        if ($loanFrom['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 days');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval

        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    }

    $issueDate = $pdo->query("SELECT lelc.due_amnt, lelc.interest_amnt, lelc.total_amnt, lelc.principal_amnt, li.issue_date
    FROM loan_issue li 
    JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id  
    JOIN customer_status cs ON cs.cus_profile_id = li.cus_profile_id
    WHERE li.cus_profile_id = '$cp_id' and cs.status >= 7 ORDER BY lelc.id DESC LIMIT 1 ");

    $loanIssue = $issueDate->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    if ($loanIssue['total_amnt'] == '' || $loanIssue['total_amnt'] == null) {
        //(For monthly interest total amount will not be there, so take principals)
        $loan_amt = intVal($loanIssue['principal_amnt']);
        $loan_type = 'interest';
    } else {
        $loan_amt = intVal($loanIssue['total_amnt']);
        $loan_type = 'emi';
    }

    $due_amt_1 = $loanIssue['due_amnt'];

    if ($loan_type == 'interest') {
        $princ_amt_1 = $loanIssue['principal_amnt'];
        $due_amt_1 = $loanIssue['interest_amnt'];
    }

    $issue_date = $loanIssue['issue_date'];
    ?>

    <thead>
        <tr><!-- Showing Collection Due Month Start and balance -->
            <th width="5%"> Due No </th>
            <th width="8%"> Due Month </th>
            <th> Month </th>
            <?php if ($loan_type == 'emi') { ?>
                <th> Due Amount </th>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <th> Principal </th>
                <th> Interest </th>
            <?php } ?>
            <th> Pending </th>
            <th> Payable </th>
            <th> Collection Date </th>
            <?php if ($loan_type == 'emi') { ?>
                <th> Collection Amount </th>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <th> Principal Amount </th>
                <th> Interest Amount </th>
            <?php } ?>
            <th> Balance Amount </th>
            <?php if ($loan_type == 'emi') { ?>
                <th> Pre Closure </th>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <th> Principal Waiver </th>
                <th> Interest Waiver </th>
            <?php } ?>
            <th> Role </th>
            <th width="8%"> User ID </th>
            <!-- <th> Collection Method </th> -->
            <th> ACTION </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td> </td>
            <td><?php
                if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                    //For Monthly.
                    echo date('m-Y', strtotime($issue_date));
                } else {
                    //For Weekly && Day.
                    echo date('d-m-Y', strtotime($issue_date));
                } ?></td>
            <td><?php echo date('M', strtotime($issue_date)); ?></td>
            <?php if ($loan_type == 'emi') { ?>
                <td> </td>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <td> </td>
                <td> </td>
            <?php } ?>
            <td></td>
            <td></td>
            <td></td>

            <!-- for collected amt -->
            <?php if ($loan_type == 'emi') { ?>
                <td> </td>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <td> </td>
                <td> </td>
            <?php } ?>

            <td><?php echo moneyFormatIndia($loan_amt); ?></td>
            <td></td>
            <?php if ($loan_type == 'interest') { ?>
                <td> </td>
            <?php } ?>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <?php
        $issued = date('Y-m-d', strtotime($issue_date));
        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
            //Query for Monthly.

            $run = $pdo->query("SELECT c.coll_code, c.due_amt,c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track,c.princ_amt_track,c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.cus_profile_id = '$cp_id' AND (c.due_amt_track != '' or c.princ_amt_track!='' or c.int_amt_track!='' or c.pre_close_waiver!='' or c.principal_waiver!='' or c.interest_waiver !='')
            AND(
                (
                    ( MONTH(c.coll_date) >= MONTH('$issued') AND YEAR(c.coll_date) = YEAR('$issued') )
                    AND 
                    ( 
                        (
                            YEAR(c.coll_date) = YEAR('$due_start_from') AND MONTH(c.coll_date) < MONTH('$due_start_from')
                        ) OR (
                            YEAR(c.coll_date) < YEAR('$due_start_from')
                        )
                    )
                ) 
                OR
                (
                    ( MONTH(c.trans_date) >= MONTH('$issued') AND YEAR(c.trans_date) = YEAR('$issued') )
                    AND 
                    ( 
                        (
                            YEAR(c.trans_date) = YEAR('$due_start_from') AND MONTH(c.trans_date) < MONTH('$due_start_from')
                        ) OR (
                            YEAR(c.trans_date) < YEAR('$due_start_from')
                        )
                            AND c.trans_date != '0000-00-00'
                    )
                )
            )");
        } else
        if ($loanFrom['scheme_due_method'] == '2') {
            //Query For Weekly.

            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='' OR c.princ_amt_track != '' OR principal_waiver !='' OR c.interest_waiver !='')
            AND (
                   (DATE(c.coll_date) >= DATE('$issued') AND DATE(c.coll_date) < DATE('$due_start_from') AND DATE(c.coll_date) != '0000-00-00' ) OR
                (DATE(c.trans_date) >= DATE('$issued') AND DATE(c.trans_date) < DATE('$due_start_from') AND DATE(c.trans_date) != '0000-00-00' )
                )
            ");
        } else
        if ($loanFrom['scheme_due_method'] == '3') {
            //Query For Day.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                (DATE(c.coll_date) >= DATE('$issued') AND DATE(c.coll_date) < DATE('$due_start_from') AND DATE(c.coll_date) != '0000-00-00' ) OR
                (DATE(c.trans_date) >= DATE('$issued') AND DATE(c.trans_date) < DATE('$due_start_from') AND DATE(c.trans_date) != '0000-00-00' )
            ) ");
        }
        $totalPaid = 0;
        $totalPreClose = 0;
        $totalPaidPrinc = 0;
        //For showing data before due start date
        $due_amt_track = 0;
        $waiver = 0;
        $principal_waiver = 0;
        $interest_waiver  = 0;
        $last_bal_amt = 0;
        $bal_amt = $loan_amt;
        if ($run->rowCount() > 0) {
            while ($row = $run->fetch()) {
                $collectionAmnt = intVal($row['due_amt_track']);
                $due_amt_track = $due_amt_track + intVal($row['due_amt_track']);
                $waiver = $waiver + intVal($row['pre_close_waiver']);
                $principal_waiver = $principal_waiver + intVal($row['principal_waiver']);
                if ($loan_type == 'interest') {
                    $PcollectionAmnt = intVal($row['princ_amt_track']);
                    $IcollectionAmnt = intVal($row['int_amt_track']);
                    $InterestwaiverAmnt =  intVal($row['interest_waiver']);
                    if ($last_bal_amt != 0) {
                        $bal_amt = $last_bal_amt - $PcollectionAmnt - $principal_waiver;
                    } else {
                        $bal_amt = $loan_amt - $PcollectionAmnt - $principal_waiver;
                    }
                } else {
                    $bal_amt = $loan_amt - $due_amt_track - $waiver;
                }
        ?>
                <tr> <!-- Showing From loan date to due start date. if incase due paid before due start date it has to show seperatly in top row. -->
                    <td></td>
                    <td></td>
                    <td></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td></td>
                        <td></td>
                    <?php } ?>
                    <td><?php $pendingMinusCollection = moneyFormatIndia(intval($row['pending_amt'])); ?></td>
                    <td><?php $payableMinusCollection = moneyFormatIndia(intVal($row['payable_amt'])); ?></td>
                    <td>
                        <?php
                        // Check if trans_date is valid (not null, not empty, and not '0000-00-00')
                        $trans_date = (!empty($row['trans_date']) && $row['trans_date'] != '0000-00-00') ? $row['trans_date'] : $row['coll_date'];
                        echo date('d-m-Y', strtotime($trans_date));
                        ?>
                    </td>

                    <!-- for collected amt -->
                    <?php if ($loan_type == 'emi') { ?>
                        <td>
                            <?php if ($row['due_amt_track'] > 0) {
                                echo moneyFormatIndia($row['due_amt_track']);
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo moneyFormatIndia($row['pre_close_waiver']);
                            } ?>
                        </td>
                    <?php } ?>

                    <?php if ($loan_type == 'interest') { ?>
                        <td>
                            <?php
                            if ($PcollectionAmnt > 0) {
                                $totalPaidPrinc += $PcollectionAmnt;
                                echo moneyFormatIndia($PcollectionAmnt);
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($IcollectionAmnt > 0) {
                                echo moneyFormatIndia($IcollectionAmnt);
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                    <?php } ?>

                    <td><?php echo moneyFormatIndia($bal_amt); ?></td>
                    <?php if ($loan_type != 'interest') { ?>
                        <td>
                            <?php
                            if ($row['pre_close_waiver'] > 0) {
                                echo moneyFormatIndia($row['pre_close_waiver']);
                            } else {
                                echo '0';
                            }
                            ?>
                        </td>
                    <?php } else { ?>
                        <td>
                            <?php
                            if ($row['principal_waiver'] > 0) {
                                echo moneyFormatIndia($row['principal_waiver']);
                            } else {
                                echo '0';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($InterestwaiverAmnt > 0) {
                                echo moneyFormatIndia($InterestwaiverAmnt);
                            } else {
                                echo 0;
                            }
                            ?>
                        </td>
                    <?php } ?>

                    <td><?php echo $row['role']; ?>
                    </td>
                    <td><?php echo $row['name']; ?></td>
                    <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self'; } elseif ($row['coll_location'] == '2') { echo 'On Spot';} elseif ($row['coll_location'] == '3') { echo 'Bank Transfer';} 
                                ?></td> -->
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                </tr>

                <?php
                if ($loan_type == 'interest') {
                    $last_bal_amt = $bal_amt;
                } else {
                }
            }
        } else {
            if ($loan_type == 'interest') {
                $last_bal_amt = $loan_amt;
            }
        }

        //For showing collection after due start date
        $due_amt_track = 0;
        $waiver = 0;
        $jj = 0;
        $last_int_amt = $due_amt_1;
        if ($loan_type == 'interest') {
            $last_princ_amt = $last_bal_amt;
        } else {
            $bal_amt = 0;
        }
        $lastCusdueMonth = '1970-00-00';
        foreach ($dueMonth as $cusDueMonth) {
            if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                //Query for Monthly.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.princ_amt_track, c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role ,c.principal_waiver,c.interest_waiver FROM `collection` c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id WHERE (c.`cus_profile_id` = $cp_id) and (c.due_amt_track != '' or c.princ_amt_track!='' or c.int_amt_track!='' or c.pre_close_waiver!='' or c.principal_waiver !='' or c.interest_waiver !='') && ((MONTH(coll_date)= MONTH('$cusDueMonth') || MONTH(trans_date)= MONTH('$cusDueMonth')) && (YEAR(coll_date)= YEAR('$cusDueMonth') || YEAR(trans_date)= YEAR('$cusDueMonth')) )");
            } elseif ($loanFrom['scheme_due_method'] == '2') {
                //Query For Weekly.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver,lelc.due_startdate, lelc.maturity_date, lelc.due_method,u.name, r.role ,c.principal_waiver,c.interest_waiver
                    FROM collection c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id 
LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id 
WHERE c.cus_profile_id = $cp_id AND (c.due_amt_track != '' OR c.pre_close_waiver != '')
  AND (
        (c.coll_date BETWEEN '$cusDueMonth' AND DATE_ADD('$cusDueMonth', INTERVAL 6 DAY))
        OR
        (c.trans_date BETWEEN '$cusDueMonth' AND DATE_ADD('$cusDueMonth', INTERVAL 6 DAY))
      )
");
            } elseif ($loanFrom['scheme_due_method'] == '3') {
                //Query For Day.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver FROM `collection` c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id WHERE (c.`cus_profile_id` = $cp_id) and (c.due_amt_track != '' or c.pre_close_waiver!='') && 
                ( 
                    ( DAY(coll_date)= DAY('$cusDueMonth') || DAY(trans_date)= DAY('$cusDueMonth') ) && 
                    ( MONTH(coll_date)= MONTH('$cusDueMonth') || MONTH(trans_date)= MONTH('$cusDueMonth') ) && 
                    ( YEAR(coll_date)= YEAR('$cusDueMonth') || YEAR(trans_date)= YEAR('$cusDueMonth') )
                )
                ");
            }

            if ($run->rowCount() > 0) {

                while ($row = $run->fetch()) {
                    $due_amt_track = intVal($row['due_amt_track']);
                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                        $princ_amt_track = intVal($row['princ_amt_track']);
                        $int_amt_track = intVal($row['int_amt_track']);
                        $interest_waiver = intVal($row['interest_waiver']);
                    }

                    $waiver = intVal($row['pre_close_waiver']);
                    $principal_waiver = intVal($row['principal_waiver']);
                    if ($loan_type == 'emi') {
                        $bal_amt = intVal($row['bal_amt']) - $due_amt_track - $waiver;
                    } else {
                        $bal_amt = intVal($last_princ_amt) - $princ_amt_track - $principal_waiver;
                    }

                ?>
                    <tr> <!-- Showing From Due Start date to Maurity date -->
                        <?php
                        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') { //this is for monthly loan to check lastcusduemonth comparision
                            if (date('Y-m', strtotime($lastCusdueMonth)) != date('Y-m', strtotime($row['coll_date']))) {
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                        ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php
                                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                                        //For Monthly.
                                        echo date('m-Y', strtotime($cusDueMonth));
                                    } else {
                                        //For Weekly && Day.
                                        echo date('d-m-Y', strtotime($cusDueMonth));
                                    }
                                    ?></td>
                                <td>
                                    <?php
                                    echo date('M', strtotime($cusDueMonth));
                                    ?>
                                </td>

                                <?php if ($loan_type == 'emi') { ?>
                                    <td><?php echo moneyFormatIndia($row['due_amt']); ?></td>
                                <?php } ?>
                                <?php if ($loan_type == 'interest') { ?>
                                    <td><?php echo moneyFormatIndia($last_princ_amt); ?></td>
                                    <td>
                                        <?php
                                        $interest_rate_calc = $loanFrom['interest_rate'];
                                        $current_principal = $last_princ_amt;
                                        $interest_calculate = $loanFrom['interest_calculate']; // 'Month' or 'Days'

                                        // Interest calculation
                                        if ($interest_calculate == 'Month') {
                                            $int = $current_principal * ($interest_rate_calc / 100);
                                        } else if ($interest_calculate == 'Days') {
                                            $int = ($current_principal * ($interest_rate_calc / 100) / 30);
                                        } else {
                                            $int = 0; // default fallback
                                        }

                                        // Round up to next multiple of 5
                                        $curInterest = ceil($int / 5) * 5;
                                        if ($curInterest < $int) {
                                            $curInterest += 5;
                                        }

                                        echo moneyFormatIndia($curInterest);
                                        ?>
                                    </td>

                                <?php } ?>


                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?php if ($loan_type != 'emi') { ?>
                                    <td></td>
                                <?php } ?>
                            <?php }
                        } else { //this is for weekly and daily loan to check lastcusduemonth comparision
                            $weekStart = date('Y-m-d', strtotime($cusDueMonth));
                            $weekEnd   = date('Y-m-d', strtotime($cusDueMonth . ' +6 days'));
                            if (!isset($printedWeek) || $printedWeek != $weekStart) {
                                $printedWeek = $weekStart;
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                            ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php
                                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                                        //For Monthly.
                                        echo date('m-Y', strtotime($cusDueMonth));
                                    } else {
                                        //For Weekly && Day.
                                        echo date('d-m-Y', strtotime($cusDueMonth));
                                    }
                                    ?></td>
                                <td>
                                    <?php
                                    echo date('M', strtotime($cusDueMonth));
                                    ?>
                                </td>

                                <?php if ($loan_type == 'emi') { ?>
                                    <td><?php echo moneyFormatIndia($row['due_amt']); ?></td>
                                <?php } ?>
                                <?php if ($loan_type == 'interest') { ?>
                                    <td><?php echo $last_princ_amt; ?></td>
                                    <td><?php echo moneyFormatIndia($row['due_amt']);
                                        $last_int_amt = moneyFormatIndia($row['due_amt']); ?></td>
                                <?php } ?>


                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                        <?php }
                        } ?>

                        <td><?php $pendingMinusCollection = moneyFormatIndia(intVal($row['pending_amt']));
                            if ($pendingMinusCollection != '') {
                                echo ($pendingMinusCollection);
                            } else {
                                echo 0;
                            } ?></td>
                        <td><?php $payableMinusCollection = moneyFormatIndia(intVal($row['payable_amt']));
                            if ($payableMinusCollection != '') {
                                echo ($payableMinusCollection);
                            } else {
                                echo 0;
                            } ?></td>
                        <td>
                            <?php
                            // Check if trans_date is valid (not null, not empty, and not '0000-00-00')
                            $trans_date = (!empty($row['trans_date']) && $row['trans_date'] != '0000-00-00') ? $row['trans_date'] : $row['coll_date'];
                            echo date('d-m-Y', strtotime($trans_date));
                            ?>
                        </td>

                        <!-- for collected amt -->
                        <?php if ($loan_type == 'emi') { ?>
                            <td>
                                <?php if ($row['due_amt_track'] > 0) {
                                    echo moneyFormatIndia($row['due_amt_track']);
                                } elseif ($row['pre_close_waiver'] > 0) {
                                    echo moneyFormatIndia($row['pre_close_waiver']);
                                } ?>
                            </td>
                        <?php } ?>

                        <?php if ($loan_type == 'interest') { ?>
                            <td>
                                <?php
                                if ($princ_amt_track > 0) {
                                    echo moneyFormatIndia($princ_amt_track);
                                } else {
                                    echo 0;
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($int_amt_track > 0) {
                                    echo moneyFormatIndia($int_amt_track);
                                } else {
                                    echo 0;
                                }
                                ?>
                            </td>
                        <?php } ?>

                        <td><?php echo moneyFormatIndia($bal_amt);
                            if ($loan_type == 'interest') {
                                $last_princ_amt = $bal_amt;
                            } ?></td>
                        <td>
                            <?php
                            if ($loan_type == 'emi') {
                                echo ($row['pre_close_waiver'] > 0)
                                    ? moneyFormatIndia($row['pre_close_waiver'])
                                    : '0';
                            } else {
                                echo ($row['principal_waiver'] > 0)
                                    ? moneyFormatIndia($row['principal_waiver'])
                                    : '0';
                            }
                            ?>
                        </td>
                        <?php if ($loan_type == 'interest') { ?>
                            <td>
                                <?php echo ($row['interest_waiver'] > 0)
                                    ? moneyFormatIndia($row['interest_waiver'])
                                    : '0'; ?>
                            </td>
                        <?php } ?>

                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self';} elseif ($row['coll_location'] == '2') {echo 'On Spot';} elseif ($row['coll_location'] == '3') {echo 'Bank Transfer';} 
                                    ?></td> -->
                        <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                    </tr>

                <?php $lastCusdueMonth = date('d-m-Y', strtotime($cusDueMonth)); //assign this cusDueMonth to check if coll date is already showed before
                }
            } else { //if not paid on due month. else part will show.
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php
                        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                            //For Monthly.
                            echo date('m-Y', strtotime($cusDueMonth));
                        } else {
                            //For Weekly && Day.
                            echo date('d-m-Y', strtotime($cusDueMonth));
                        } ?></td>
                    <td> <?php echo date('M', strtotime($cusDueMonth)); ?> </td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td><?php echo moneyFormatIndia($due_amt_1); ?></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td><?php echo moneyFormatIndia($last_princ_amt); ?></td>
                        <td>
                            <?php
                            $interest_rate_calc = $loanFrom['interest_rate'];
                            $current_principal = $last_princ_amt;
                            $interest_calculate = $loanFrom['interest_calculate']; // 'Month' or 'Days'

                            // Interest calculation
                            if ($interest_calculate == 'Month') {
                                $int = $current_principal * ($interest_rate_calc / 100);
                            } else if ($interest_calculate == 'Days') {
                                $int = ($current_principal * ($interest_rate_calc / 100) / 30);
                            } else {
                                $int = 0; // default fallback
                            }

                            // Round up to next multiple of 5
                            $curInterest = ceil($int / 5) * 5;
                            if ($curInterest < $int) {
                                $curInterest += 5;
                            }

                            echo moneyFormatIndia($curInterest);
                            ?>
                        </td>
                        <!-- <td><?php echo $last_int_amt; ?></td> -->
                    <?php } ?>

                    <?php
                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                        if (date('Y-m', strtotime($cusDueMonth)) <=  date('Y-m')) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo moneyFormatIndia($response['pending']); ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo moneyFormatIndia($response['payable']); ?>
                            </td>
                        <?php } else if (date('Y-m', strtotime($cusDueMonth)) >  date('Y-m') && $curDateChecker == true) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                        <?php
                            $curDateChecker = false; //set to false because, pending and payable only need one month after current month
                        } else {
                        ?>
                            <td></td>
                            <td></td>
                        <?php
                        }
                    } else {
                        if (date('Y-m-d', strtotime($cusDueMonth)) <=  date('Y-m-d')) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo moneyFormatIndia($response['pending']); ?>

                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo moneyFormatIndia($response['payable']); ?>
                            </td>
                        <?php } else if (date('Y-m-d', strtotime($cusDueMonth)) >  date('Y-m-d') && $curDateChecker == true) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                        <?php
                            $curDateChecker = false; //set to false because, pending and payable only need one month after current month
                        } else {
                        ?>
                            <td></td>
                            <td></td>
                    <?php
                        }
                    }
                    ?>

                    <td></td>
                    <!-- for collected amt -->
                    <?php if ($loan_type == 'emi') { ?>
                        <td> </td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td> </td>
                        <td> </td>
                    <?php } ?>

                    <td></td>
                    <td></td>
                    <?php if ($loan_type == 'interest') { ?>
                        <td> </td>
                    <?php } ?>
                    <td></td>
                    <td></td>
                    <!-- <td></td> -->
                    <td></td>
                </tr>

            <?php
                $i++;
            }
        }

        $currentMonth = date('Y-m-d');
        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
            //Query for Monthly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt,c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track,c.princ_amt_track,c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.princ_amt_track!='' or c.int_amt_track!='' or c.pre_close_waiver!='' or c.principal_waiver!='' or c.interest_waiver !='')
             AND (
                (DATE (c.coll_date) BETWEEN '$maturity_month' AND '$currentMonth' AND c.coll_date != '0000-00-00')
                OR
                (DATE (c.trans_date) BETWEEN '$maturity_month' AND '$currentMonth' AND c.trans_date != '0000-00-00'))
          ");
        } else
        if ($loanFrom['scheme_due_method'] == '2') {
            //Query For Weekly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                (DATE(c.coll_date) > DATE('$maturity_month') AND Year(c.coll_date) > Year('$maturity_month') AND DATE(c.coll_date) <= DATE('$currentMonth') AND Year(c.coll_date) <= Year('$currentMonth') AND DATE(c.coll_date) != '0000-00-00' ) OR
                (DATE(c.trans_date) > DATE('$maturity_month') AND Year(c.trans_date) > Year('$maturity_month') AND DATE(c.trans_date) <= DATE('$currentMonth') AND Year(c.trans_date) <= Year('$currentMonth') AND DATE(c.trans_date) != '0000-00-00' )
                ) ");
        } else
        if ($loanFrom['scheme_due_method'] == '3') {
            //Query For Day.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role,c.principal_waiver,c.interest_waiver
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                    (DATE(c.coll_date) > DATE('$maturity_month') AND Year(c.coll_date) > Year('$maturity_month') AND DATE(c.coll_date) <= DATE('$currentMonth') AND Year(c.coll_date) <= Year('$currentMonth') AND DATE(c.coll_date) != '0000-00-00' ) OR
                    (DATE(c.trans_date) > DATE('$maturity_month') AND Year(c.trans_date) > Year('$maturity_month') AND DATE(c.trans_date) <= DATE('$currentMonth') AND Year(c.trans_date) <= Year('$currentMonth') AND DATE(c.trans_date) != '0000-00-00' )
                ) ");
        }

        if ($run->rowCount() > 0) {
            $due_amt_track = 0;
            $waiver = 0;
            while ($row = $run->fetch()) {
                $collectionAmnt = intVal($row['due_amt_track']);
                $due_amt_track = intVal($row['due_amt_track']);
                $waiver = intVal($row['pre_close_waiver']);
                $principal_waiver = intVal($row['principal_waiver']);
                $bal_amt = $bal_amt - $due_amt_track - $waiver;
                if ($loan_type == 'interest') {
                    $PcollectionAmnt = intVal($row['princ_amt_track']);
                    $IcollectionAmnt = intVal($row['int_amt_track']);
                    if ($last_bal_amt != 0) {
                        $bal_amt = $last_bal_amt - $PcollectionAmnt - $principal_waiver;
                    } else {
                        $bal_amt = $loan_amt - $PcollectionAmnt - $principal_waiver;
                    }
                } else {
                    $bal_amt = $loan_amt - $due_amt_track - $waiver;
                }
            ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td></td>
                        <td></td>
                    <?php } ?>

                    <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                        if ($pendingMinusCollection != '') {
                            echo moneyFormatIndia($pendingMinusCollection);
                        } else {
                            echo 0;
                        } ?></td>
                    <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                        if ($payableMinusCollection != '') {
                            echo moneyFormatIndia($payableMinusCollection);
                        }
                        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td>
                            <?php if ($row['due_amt_track'] > 0) {
                                echo moneyFormatIndia($row['due_amt_track']);
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo moneyFormatIndia($row['pre_close_waiver']);
                            } ?>
                        </td>
                    <?php } ?>

                    <?php if ($loan_type == 'interest') { ?>
                        <td>
                            <?php if ($PcollectionAmnt > 0) {
                                $totalPaidPrinc += $PcollectionAmnt;
                                echo moneyFormatIndia($PcollectionAmnt);
                            } else {
                                echo 0;
                            } ?>
                        </td>
                        <td>
                            <?php if ($IcollectionAmnt > 0) {
                                echo moneyFormatIndia($IcollectionAmnt);
                            } else {
                                echo 0;
                            } ?>
                        </td>
                    <?php } ?>

                    <td><?php echo moneyFormatIndia($bal_amt); ?></td>
                    <?php if ($loan_type != 'interest') { ?>
                        <td>
                            <?php
                            if ($row['pre_close_waiver'] > 0) {
                                echo moneyFormatIndia($row['pre_close_waiver']);
                            } else {
                                echo '0';
                            }
                            ?>
                        </td>
                    <?php } else { ?>
                        <td>
                            <?php
                            if ($row['principal_waiver'] > 0) {
                                echo moneyFormatIndia($row['principal_waiver']);
                            } else {
                                echo '0';
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td>
                            <?php
                            if ($row['interest_waiver'] > 0) {
                                echo moneyFormatIndia($row['interest_waiver']);
                            } else {
                                echo '0';
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <td><?php echo $row['role']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self';} elseif ($row['coll_location'] == '2') {echo 'On Spot';} elseif ($row['coll_location'] == '3') {echo 'Bank Transfer';} 
                                ?></td> -->
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                </tr>
                <?php
                if ($loan_type == 'interest') {
                    $last_bal_amt = $bal_amt;
                } ?>
        <?php
                $i++;
            }
        }
        ?>

    </tbody>
</table>

<?php
function getNextLoanDetails($pdo, $cp_id, $date)
{
    $loan_arr = array();
    $coll_arr = array();
    $response = array(); //Final array to return

    $result = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE cus_profile_id = $cp_id ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $loan_arr = $row;

        if ($loan_arr['total_amnt'] == '' || $loan_arr['total_amnt'] == null) {
            //(For monthly interest total amount will not be there, so take principals)
            $response['total_amt'] = $loan_arr['principal_amnt'];
            $response['loan_type'] = 'interest';
            $loan_arr['loan_type'] = 'interest';
        } else {
            $response['total_amt'] = $loan_arr['total_amnt'];
            $response['loan_type'] = 'emi';
            $loan_arr['loan_type'] = 'emi';
        }
        $response['interest_calculate'] = $loan_arr['interest_calculate'];
        if ($loan_arr['due_amnt'] == '' || $loan_arr['due_amnt'] == null) {
            //(For monthly interest Due amount will not be there, so take interest)
            $response['due_amt'] = $loan_arr['interest_amnt'];
        } else {
            $response['due_amt'] = $loan_arr['due_amnt']; //Due amount will remain same
        }
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_profile_id = $cp_id ");
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch()) {
            $coll_arr[] = $row;
        }
        $total_paid = 0;
        $total_paid_princ = 0;
        $total_paid_int = 0;
        $pre_closure = 0;

        foreach ($coll_arr as $tot) {
            $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
            $pre_closure += intVal($tot['pre_close_waiver']); //get pre closure value to subract to get balance amount
            $total_paid_princ += intVal($tot['princ_amt_track']);
            $total_paid_int += intVal($tot['int_amt_track']);
        }
        //total paid amount will be all records again request id should be summed
        $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
        $response['total_paid_int'] = $total_paid_int;
        $response['pre_closure'] = $pre_closure;

        //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
        $response['balance'] = $response['total_amt'] - $response['total_paid'] - $pre_closure;

        if ($loan_arr['loan_type'] == 'interest') {
            $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance'], $response['interest_calculate']);
        }

        $response = calculateOthers($loan_arr, $response, $date, $pdo, $cp_id);
    } else {
        //If collection table dont have rows means there is no payment against that request, so total paid will be 0
        $response['total_paid'] = 0;
        $response['total_paid_int'] = 0;
        $response['pre_closure'] = 0;
        //If in collection table, there is no payment means balance amount still remains total amount
        $response['balance'] = $response['total_amt'];

        if ($loan_arr['loan_type'] == 'interest') {
            $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance'], $response['interest_calculate']);
        }

        $response = calculateOthers($loan_arr, $response, $date, $pdo, $cp_id);
    }

    //To get the collection charges
    $result = $pdo->query("SELECT SUM(coll_charge) as coll_charge FROM `collection_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
    $row = $result->fetch();
    if ($row['coll_charge'] != null) {

        $coll_charges = $row['coll_charge'];

        $result = $pdo->query("SELECT SUM(coll_charge_track) as coll_charge_track,SUM(coll_charge_waiver) as coll_charge_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            $coll_charge_track = $row['coll_charge_track'];
            $coll_charge_waiver = $row['coll_charge_waiver'];
        } else {
            $coll_charge_track = 0;
            $coll_charge_waiver = 0;
        }

        $response['coll_charge'] = $coll_charges - $coll_charge_track - $coll_charge_waiver;
    } else {
        $response['coll_charge'] = 0;
    }

    return $response;
}
function calculateOthers($loan_arr, $response, $date, $pdo, $cp_id)
{

    //***************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_startdate'];
    $maturity_month = $loan_arr['maturity_date'];

    $tot_paid_tilldate = 0;
    $preclose_tilldate = 0;


    $checkcollection = $pdo->query("SELECT SUM(`due_amt_track`) as totalPaidAmt FROM `collection` WHERE `cus_profile_id` = '$cp_id'"); // To Find total paid amount till Now.
    $checkrow = $checkcollection->fetch();
    $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0; //null collation operator
    $checkack = $pdo->query("SELECT interest_amnt,due_amnt FROM `loan_entry_loan_calculation` WHERE `cus_profile_id` = '$cp_id'"); // To Find Due Amount.
    $checkAckrow = $checkack->fetch();
    $int_amt_cal = $checkAckrow['interest_amnt'];
    $due_amt = $checkAckrow['due_amnt'];

    if ($loan_arr['due_method'] == 'Monthly' || $loan_arr['scheme_due_method'] == '1') {
        if ($loan_arr['loan_type'] != 'interest') {

            //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
            $due_start_from = date('Y-m', strtotime($due_start_from));
            $maturity_month = date('Y-m', strtotime($maturity_month));

            // Create a DateTime object from the given date
            $maturity_month = new DateTime($maturity_month);
            // Subtract one month from the date
            // $maturity_month->modify('-1 month');
            // Format the date as a string
            $maturity_month = $maturity_month->format('Y-m');

            //If Due method is Monthly, Calculate penalty by checking the month has ended or not
            $current_date = date('Y-m', strtotime($date));

            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

            $interval = new DateInterval('P1M'); // Create a one month interval
            //condition start
            $count = 0;
            $loandate_tillnow = 0;
            $countForPenalty = 0;

            $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
            $start = DateTime::createFromFormat('Y-m', $due_start_from);
            $current = DateTime::createFromFormat('Y-m', $current_date);



            for ($i = $start; $i < $current; $start->add($interval)) {
                $loandate_tillnow += 1;
                $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
            }

            while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.
                $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
                $penalty_date  = $start_date_obj->format('Y-m');
                $start_date_obj->add($interval);

                $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((MONTH(coll_date)= MONTH('$penalty_checking_date') || MONTH(trans_date)= MONTH('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
                $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
                }
                $row = $result->fetch();
                $penalty_per = $row['overdue']; //get penalty percentage to insert
                $penalty = round(($response['due_amt'] * $penalty_per) / 100);

                if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                    $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_date' and cus_profile_id = '$cp_id' ");
                    if ($checkPenalty->rowCount() == 0) {
                        if ($loan_arr['loan_type'] == 'emi') {
                            //if loan type is emi then directly apply penalty when month crossed and above conditions true
                        } else if ($loan_arr['loan_type'] == 'interest' and  $count != 0) {
                            // if loan type is interest then apply penalty if the loop month is not first
                            // so penalty should not raise, coz a month interest is paid after the month end
                        }
                    }
                    $countForPenalty++;
                }

                $count++; //Count represents how many months are exceeded
            }
            //condition END

            //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
            $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track, sum(pre_close_waiver) as pre_close_waiver from `collection` 
        where cus_profile_id = $cp_id 
        AND 
        ( 
            ( ( YEAR(trans_date) = YEAR('$date') AND MONTH(trans_date) <= MONTH('$date') ) OR ( YEAR(trans_date) < YEAR('$date') ) AND trans_date !='0000-00-00') 
            OR 
            ( ( YEAR(coll_date) = YEAR('$date') AND MONTH(coll_date) <= MONTH('$date') ) OR ( YEAR(coll_date) < YEAR('$date') ) )
        ) ");
            if ($qry->rowCount() > 0) {
                $rowss = $qry->fetch();
                $tot_paid_tilldate = intVal($rowss['due_amt_track']);
                $preclose_tilldate = intVal($rowss['pre_close_waiver']);
            }
            if ($count > 0) {

                //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
                $response['pending'] = ($response['due_amt'] * ($count)) - $tot_paid_tilldate - $preclose_tilldate;
                // If due month exceeded
                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
                }
                $row = $result->fetch();
                $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

                // to get overall penalty paid till now to show pending penalty amount
                $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
                $row = $result->fetch();
                if ($row['penalty'] == null) {
                    $row['penalty'] = 0;
                }
                if ($row['penalty_waiver'] == null) {
                    $row['penalty_waiver'] = 0;
                }
                //to get overall penalty raised till now for this req id
                $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
                $row1 = $result1->fetch();
                if ($row1['penalty'] == null) {
                    $penalty = 0;
                } else {
                    $penalty = $row1['penalty'];
                }

                $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];


                //Payable amount will be pending amount added with current month due amount
                $response['payable'] = $response['due_amt'] + $response['pending'];

                if ($response['payable'] > $response['balance']) {
                    //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                    //this case will occur when collection status becoms OD
                    $response['payable'] = $response['balance'];
                }
            } else {
                //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
                $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
                //If still current month is not ended, then penalty will be 0
                $response['penalty'] = 0;
                //If still current month is not ended, then payable will be due amt
                $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;
            }
        } else {

            $interest_details = calculateInterestLoan($pdo, $loan_arr, $response, $cp_id, $date);
            $all_data = array_merge($response, $interest_details);
            $response = $all_data;
        }
    } else
    if ($loan_arr['scheme_due_method'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

        $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
        $start = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $current = DateTime::createFromFormat('Y-m-d', $current_date);

        for ($i = $start; $i < $current; $start->add($interval)) {
            $loandate_tillnow += 1;
            $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
        }

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.

            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((WEEK(coll_date)= WEEK('$penalty_checking_date') || WEEK(trans_date)= WEEK('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $penalty = round(($response['due_amt'] * $penalty_per) / 100);
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                }
                $countForPenalty++;
            }
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart

        $qry = $pdo->query("SELECT SUM(due_amt_track) AS due_amt_track, SUM(pre_close_waiver) AS pre_close_waiver FROM collection c
WHERE c.cus_profile_id = '$cp_id'  AND (
       (trans_date <= DATE_ADD('$current_date', INTERVAL 6 DAY) AND trans_date <> '0000-00-00')
       OR
       (coll_date <= DATE_ADD('$current_date', INTERVAL 6 DAY) AND coll_date <> '0000-00-00')
  );
");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
            $preclose_tilldate = intVal($rowss['pre_close_waiver']);
        }
        if ($count > 0) {
            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $tot_paid_tilldate - $preclose_tilldate;
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();
            if ($row['penalty'] == null) {
                $row['penalty'] = 0;
            }
            if ($row['penalty_waiver'] == null) {
                $row['penalty_waiver'] = 0;
            }
            //to get overall penalty raised till now for this req id
            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row1 = $result1->fetch();
            if ($row1['penalty'] == null) {
                $penalty = 0;
            } else {
                $penalty = $row1['penalty'];
            }

            $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];

            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;
        }
    } elseif ($loan_arr['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

        $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
        $start = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $current = DateTime::createFromFormat('Y-m-d', $current_date);

        for ($i = $start; $i < $current; $start->add($interval)) {
            $loandate_tillnow += 1;
            $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
        }

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.
            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((DAY(coll_date)= DAY('$penalty_checking_date') || DAY(trans_date)= DAY('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $penalty = round(($response['due_amt'] * $penalty_per) / 100);
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                }
                $countForPenalty++;
            }
        }
        //condition END
        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track, sum(pre_close_waiver) as pre_close_waiver from `collection` where cus_profile_id = $cp_id and (date(coll_date) <= date('$current_date') or date(trans_date) <= date('$current_date')) ");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
            $preclose_tilldate = intVal($rowss['pre_close_waiver']);
        }
        if ($count > 0) {


            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $tot_paid_tilldate - $preclose_tilldate;

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();
            if ($row['penalty'] == null) {
                $row['penalty'] = 0;
            }
            if ($row['penalty_waiver'] == null) {
                $row['penalty_waiver'] = 0;
            }
            //to get overall penalty raised till now for this req id
            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row1 = $result1->fetch();
            if ($row1['penalty'] == null) {
                $penalty = 0;
            } else {
                $penalty = $row1['penalty'];
            }

            $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }
        } else {
            //If still current month is not ended, then pending will be same due amt// pending will be 0 if due date not exceeded
            $response['pending'] = 0; //$response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;
        }
    }

    if ($response['pending'] < 0) {
        $response['pending'] = 0;
    }
    if ($response['payable'] < 0) {
        $response['payable'] = 0;
    }
    return $response;
}

function calculateNewInterestAmt($int_rate, $balance, $calculate_method)
{
    if ($calculate_method == 'Month') {
        $int = $balance * ($int_rate / 100);
    } else if ($calculate_method == 'Days') {
        $int = ($balance * ($int_rate / 100) / 30);
    }

    // Use your clean rounding logic
    $curInterest = ceilAmount($int);

    $response = $curInterest;

    return $response;
}

function calculateInterestLoan($pdo, $loan_arr, $response, $cp_id, $date)
{
    $due_start_from = $loan_arr['loan_date'];
    $maturity_month = $loan_arr['maturity_date'];

    // Convert Date to Year and month
    $due_start_from = date('Y-m', strtotime($due_start_from));
    $maturity_month = date('Y-m', strtotime($maturity_month));
    $calc_date = date('Y-m', strtotime($date)); // ✅ Use the passed date

    // Create a DateTime object for maturity month
    $maturity_month = new DateTime($maturity_month);
    $maturity_month->modify('-1 month'); // maturity should include last month due
    $maturity_month = $maturity_month->format('Y-m');

    $start_date_obj   = DateTime::createFromFormat('Y-m', $due_start_from);
    $end_date_obj     = DateTime::createFromFormat('Y-m', $maturity_month);
    $current_date_obj = DateTime::createFromFormat('Y-m', $calc_date); // ✅ use $date

    $interval = new DateInterval('P1M');
    // Count how many months crossed
    $count = 0;
    while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
        $start_date_obj->add($interval);
        $count++;
    }
    if ($start_date_obj >= $end_date_obj) {
        $count++; // include maturity month
    }
    $res['count_of_month'] = $count;

    $interest_paid = getPaidInterest($pdo, $cp_id, $date);

    if ($count > 0) {
        $res['payable']      = payableCalculation($pdo, $loan_arr, $response, $cp_id, $date) - $interest_paid;
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'curmonth', $cp_id, $date) - $interest_paid;
        $res['pending']      = pendingCalculation($pdo, $loan_arr, $response, $cp_id, $date) - $interest_paid;

        if ($res['pending'] < 0)  $res['pending'] = 0;
        if ($res['payable'] < 0)  $res['payable'] = 0;
    } else {
        // Before due start month – only till-date interest
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'forstartmonth', $cp_id, $date) - $interest_paid;
        $res['pending'] = 0;
        $res['payable'] = 0;
        $res['penalty'] = 0;
    }

    // Round off
    $res['payable']      = ceilAmount($res['payable']);
    $res['pending']      = ceilAmount($res['pending']);
    $res['till_date_int'] = ceilAmount($res['till_date_int']);

    return $res;
}

function getTillDateInterest($loan_arr, $response, $pdo, $data, $cp_id, $date)
{
    $result = 0; // default

    if ($data == 'forstartmonth') {
        // To calculate till date Interest if loan is interest based
        if ($loan_arr['loan_type'] == 'interest') {

            // Loan issued date
            $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

            // Use passed date instead of today's date
            $cur_date = new DateTime(date('Y-m-d', strtotime($date)));

            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, '', $cp_id);

            // Round up till nearest multiple of 5
            $cur_amt = ceil($result / 5) * 5;
            if ($cur_amt < $result) {
                $cur_amt += 5;
            }
            $result = $cur_amt;
        }
        return $result;
    }

    if ($data == 'curmonth') {
        $cur_date = new DateTime(date('Y-m-d', strtotime($date))); // ✅ use passed date
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

        $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'TDI', $cp_id);
        return $result;
    }

    if ($data == 'pendingmonth') {
        // for pending value check, goto 2 months before
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

        // Take passed date, then go 2 months before and set to last day
        $cur_date = new DateTime(date('Y-m-d', strtotime($date)));
        $cur_date->modify('-2 months');
        $cur_date->modify('last day of this month');

        $result = 0;
        if ($issued_date <= $cur_date) {
            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'pending', $cp_id);
        }
        return $result;
    }

    return $response;
}

function payableCalculation($pdo, $loan_arr, $response, $cp_id, $date)
{
    $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
    $cur_date    = new DateTime(date('Y-m-d', strtotime($date))); // ✅ use passed date
    $result = 0;

    if ($response['interest_calculate'] == "Month") {
        // last month based on given date
        $last_month = clone $cur_date;
        $last_month->modify('-1 month');

        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_month->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date; // fresh copy

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);

            $st_date->modify('+1 month')->modify('first day of this month');
        }
    } elseif ($response['interest_calculate'] == "Days") {
        $last_date = clone $cur_date;
        $last_date->modify('-1 month'); // go back one month from given date

        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_date->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date;

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);

            $st_date->modify('+1 month')->modify('first day of this month');
        }
    }

    return $result;
}

// ----------------- Due Amount Calculation -----------------
function dueAmtCalculation($pdo, $start_date, $end_date, $due_amt, $loan_arr, $status, $cp_id)
{
    $start = new DateTime($start_date->format('Y-m-d'));
    $end = new DateTime($end_date->format('Y-m-d'));

    $interest_calculate = $loan_arr['interest_calculate'];
    $int_rate = $loan_arr['interest_rate'];
    $result = 0;

    $loanRow = $pdo->query("SELECT loan_amnt FROM loan_entry_loan_calculation WHERE cus_profile_id = '" . $cp_id . "'")->fetch(PDO::FETCH_ASSOC);
    $current_balance = $loanRow['loan_amnt'];

    $collections = $pdo->query("SELECT princ_amt_track, principal_waiver, coll_date 
        FROM collection 
        WHERE cus_profile_id = '" . $cp_id . "' 
          AND (princ_amt_track != '' OR principal_waiver != '') 
        ORDER BY coll_date ASC")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($collections)) {
        $collection_index = 0;

        while ($start <= $end) {
            $today_str = $start->format('Y-m-d');
            $paid_principal_today = 0;
            $paid_principal_waiver = 0;

            while ($collection_index < count($collections)) {
                $coll_date = (new DateTime($collections[$collection_index]['coll_date']))->format('Y-m-d');
                if ($coll_date == $today_str) {
                    $paid_principal_today += (float)$collections[$collection_index]['princ_amt_track'];
                    $paid_principal_waiver += (float)$collections[$collection_index]['principal_waiver'];
                    $collection_index++;
                } else {
                    break;
                }
            }

            $current_balance = max(0, $current_balance - ($paid_principal_today + $paid_principal_waiver));

            $interest_today = calculateNewInterestAmt($int_rate, $current_balance, $interest_calculate);

            if ($interest_calculate === 'Days') {
                $result += $interest_today;
            } else {
                $days_in_month = (int)$start->format('t');
                $daily_interest = $interest_today / $days_in_month;
                $result += $daily_interest;
            }

            $start->modify('+1 day');
        }
    } else {
        // No collections
        if ($interest_calculate == 'Month') {
            while ($start->format('Y-m') <= $end->format('Y-m')) {
                $dueperday = $due_amt / intval($start->format('t'));
                $new_end = clone $start;
                $new_end->modify('last day of this month');

                $days_count = ($start->diff($new_end))->days + 1;
                $result += $days_count * $dueperday;

                $start->modify('+1 month')->modify('first day of this month');
            }
        } else { // Days
            while ($start <= $end) {
                $result += $due_amt;
                $start->modify('+1 day');
            }
        }
    }

    return $result;
}

// ----------------- Pending Calculation -----------------
function pendingCalculation($pdo, $loan_arr, $response, $cp_id, $date)
{
    $pending = getTillDateInterest($loan_arr, $response, $pdo, 'pendingmonth', $cp_id, $date);
    return $pending;
}

// ----------------- Paid Interest Calculation -----------------
function getPaidInterest($pdo, $cp_id, $date)
{
    // Only consider collections up to the given date
    $qry = $pdo->prepare("SELECT COALESCE(SUM(int_amt_track),0) + COALESCE(SUM(interest_waiver),0) AS int_paid
        FROM collection 
        WHERE cus_profile_id = :cp_id 
          AND (int_amt_track != '' AND int_amt_track IS NOT NULL OR interest_waiver != '' AND interest_waiver IS NOT NULL)
          AND coll_date <= :date
    ");
    $qry->execute([
        ':cp_id' => $cp_id,
        ':date'  => date('Y-m-d', strtotime($date))
    ]);

    $int_paid = $qry->fetch(PDO::FETCH_ASSOC)['int_paid'] ?? 0;
    return intval($int_paid);
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
?>