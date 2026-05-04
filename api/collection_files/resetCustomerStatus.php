<?php
require '../../ajaxconfig.php';

if (isset($_POST['cus_id'])) {
    $cus_id =  $_POST['cus_id'];
}

$cp_arr = array();
if (isset($cus_id)) {
    $qry = $pdo->query("SELECT li.cus_profile_id as cp_id FROM loan_issue li JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id  where li.cus_id = '$cus_id' and cs.status =7 and li.balance_amount=0 ORDER BY li.cus_profile_id DESC ");
    while ($row = $qry->fetch()) {
        $cp_arr[] = $row['cp_id'];
    }
}

if (isset($_POST['cpID'])) {
    $cpid = $_POST['cpID'];
    $cp_arr[] = $cpid;
    $qry = $pdo->query("SELECT cus_id FROM loan_issue where cus_profile_id = $cpid ");
    $row = $qry->fetch();
    $cus_id = $row['cus_id'];
}
//get Total amt from ack loan calculation (For monthly interest total amount will not be there, so take principals)*
//get Paid amt from collection table if nothing paid show 0*
//balance amount is Total amt - paid amt*
//get Due amt from ack loan calculation*
//get Pending amt from collection based on last entry against request id (Due amt - paid amt)
//get Payable amt by adding pending and due amount
//get penalty, if due date exceeded put the penalty percentage to the due amt
//get collection charges from collection charges table if exists else 0
$loan_arr = array();
$coll_arr = array();
$response = array(); //Final array to return

$cp_id = 0;
$i = 0;
foreach ($cp_arr as $cp_id) {

    $response['cp_id'][$i] = $cp_id;
    $result = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE cus_profile_id = $cp_id ");
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

        $response['interest_calculate'] = $loan_arr['interest_calculate'];
        if ($loan_arr['due_amnt'] == '' || $loan_arr['due_amnt'] == null) {
            //(For monthly interest Due amount will not be there, so take interest)
            $response['due_amt'] = intVal($loan_arr['interest_amnt']);
        } else {
            $response['due_amt'] = intVal($loan_arr['due_amnt']); //Due amount will remain same
        }
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_profile_id ='" . $cp_id . "' ");
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
        //total paid amount will be all records again cp id should be summed
        $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
        $response['total_waiver'] = ($loan_arr['loan_type'] == 'emi') ? $pre_closure : $principal_waiver;
        $response['total_paid_int'] = $total_paid_int;
        $response['pre_closure'] = $pre_closure;
        $response['principal_waiver'] = $principal_waiver;

        //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
        $response['balance'] = $response['total_amt'] - $response['total_paid'] -  $response['total_waiver'];

        if ($loan_arr['loan_type'] == 'interest') {
            $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance'], $response['interest_calculate']);
        }

        $response = calculateOthers($loan_arr, $response, $pdo, $cp_id);
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

        $response = calculateOthers($loan_arr, $response, $pdo, $cp_id);
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

    $response['payable_as_req'][$i] = $response['payable'];
    $response['pending_as_req'][$i] = $response['pending'];

    //Pending Check
    if ($response['pending'] > 0 && $response['count_of_month'] != 0) {
        $response['pending_customer'][$i] = true;
    } else {
        $response['pending_customer'][$i] = false;
    }
    //OD check
    if ($response['od'] == true) {
        $response['od_customer'][$i] = true;
    } else {
        $response['od_customer'][$i] = false;
    }

    //Due nill Check
    if ($response['due_nil'] == true) {
        $response['due_nil_customer'][$i] = true;
    } else {
        $response['due_nil_customer'][$i] = false;
    }

    $response['balAmnt'][$i] =  $response['balance'];

    $i++;
}
//for knowing the customer status for due followup screen
//this will give the customer's sub status in the order of Legal, Error, OD, Due Nill, Pending, Current
$response['follow_cus_sts'] = checkStatusOfCustomer($response, $loan_arr, $cus_id, $pdo);

function calculateOthers($loan_arr, $response, $pdo, $cp_id)
{
    //**************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_startdate'];
    $maturity_month = $loan_arr['maturity_date'];

    if ($loan_arr['due_method'] == 'Monthly' || $loan_arr['scheme_due_method'] == '1') {
        if ($loan_arr['loan_type'] != 'interest') {
            //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
            $due_start_from = date('Y-m', strtotime($due_start_from));
            $maturity_month = date('Y-m', strtotime($maturity_month));

            //If Due method is Monthly, Calculate penalty by checking the month has ended or not
            $current_date = date('Y-m');


            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

            $interval = new DateInterval('P1M'); // Create a one month interval

            $count = 0;

            while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
                //To raise penalty in seperate table
                $penalty_raised_date  = $start_date_obj->format('Y-m');
                // If due month exceeded
                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
                }
                $row = $result->fetch();
                $penalty_per = $row['overdue']; //get penalty percentage to insert
                $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);

                $start_date_obj->add($interval);
                $count++; //Count represents how many months are exceeded
            }

            $response['count_of_month'] = $count;
            //To check over due, if current date is greater than maturity minth, then i will be OD
            if ($current_date_obj > $end_date_obj) {
                $response['od'] = true;
            } else {
                $response['od'] = false;
            }

            //To check whether due has been nil with other charges

            $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id ");
            $row = $qry->fetch();

            $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);

            if ($loan_arr['loan_type'] == 'interest') {
                $due_amt_track = $row['princ_amt_track'] + $row['pre_close_waiver'];
            }

            //if sum value is null or empty then assign 0 to it
            if ($row['penalty'] == '' or $row['penalty'] == null) {
                $row['penalty'] = 0;
            }
            if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
                $row['paid_amntpc'] = 0;
            }
            if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
                $row['waiver_amntpc'] = 0;
            }
            if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
                $row['coll_charge'] = 0;
            }
            if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
                $row['paid_amntcc'] = 0;
            }
            if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
                $row['waiver_amntcc'] = 0;
            }

            $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
            $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

            $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
            $row = $qry->fetch();

            if ($row['tot_amt_cal'] != 0) {
                $total_for_nil = $row['tot_amt_cal'];
            } else {
                $total_for_nil = $row['principal_amt_cal'];
            }
            $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

            if ($due_nil_check == 0) {
                if ($curr_penalty > 0 || $curr_charges > 0) {
                    $response['due_nil'] = true;
                } else {
                    $response['due_nil'] = false;
                }
            } else {
                $response['due_nil'] = false;
            }

            if ($count > 0) {

                //if Due month exceeded due amount will be as pending with how many months are exceeded
                $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

                // If due month exceeded
                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
                }
                $row = $result->fetch();
                $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

                $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
                $row = $result->fetch();

                $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
                $response['penalty'] = intval($penalty) - (($row['penalty']) ? $row['penalty'] : 0) - (($row['penalty_waiver']) ? $row['penalty_waiver'] : 0);

                //Payable amount will be pending amount added with current month due amount
                $response['payable'] = $response['due_amt'] + $response['pending'];
            } else {
                //If still current month is not ended, then pending will be same due amt
                $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
                //If still current month is not ended, then penalty will be 0
                $response['penalty'] = 0;
                //If still current month is not ended, then payable will be due amt
                $response['payable'] = 0;
            }
        } else {

            $interest_details = calculateInterestLoan($pdo, $loan_arr, $response, $cp_id);
            $all_data = array_merge($response, $interest_details);
            $response = $all_data;
        }
    } else
    if ($loan_arr['scheme_due_method'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        $count = 0;
        // $qry = $pdo->query("DELETE FROM penalty_charges where req_id = '$cp_id' and (penalty_date != '' or penalty_date != NULL ) ");
        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
            //To raise penalty in seperate table
            $penalty_raised_date  = $start_date_obj->format('Y-m-d');
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);

            $start_date_obj->add($interval);
            $count++; //Count represents how many months are exceeded
        }
        $response['count_of_month'] = $count;
        //To check over due, if current date is greater than maturity minth, then i will be OD
        if ($current_date_obj > $end_date_obj) {
            $response['od'] = true;
        } else {
            $response['od'] = false;
        }

        //To check whether due has been nil with other charges

        $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id ");
        $row = $qry->fetch();

        $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);
        //if sum value is null or empty then assign 0 to it
        if ($row['penalty'] == '' or $row['penalty'] == null) {
            $row['penalty'] = 0;
        }
        if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
            $row['paid_amntpc'] = 0;
        }
        if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
            $row['waiver_amntpc'] = 0;
        }
        if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
            $row['coll_charge'] = 0;
        }
        if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
            $row['paid_amntcc'] = 0;
        }
        if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
            $row['waiver_amntcc'] = 0;
        }

        $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
        $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

        $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
        $row = $qry->fetch();

        if ($row['tot_amt_cal'] != '') {
            $total_for_nil = $row['tot_amt_cal'];
        } else {
            $total_for_nil = $row['principal_amt_cal'];
        }
        $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

        if ($due_nil_check == 0) {
            if ($curr_penalty > 0 || $curr_charges > 0) {
                $response['due_nil'] = true;
            }
        } else {
            $response['due_nil'] = false;
        }

        if ($count > 0) {

            //if Due month exceeded due amount will be as pending with how many months are exceeded
            $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
            $response['penalty'] = intval($penalty) - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
        } else {
            //If still current month is not ended, then pending will be same due amt
            $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = 0;
        }
    } elseif ($loan_arr['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval

        $count = 0;

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
            //To raise penalty in seperate table
            $penalty_raised_date  = $start_date_obj->format('Y-m-d');
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert

            $penalty = ($response['due_amt'] * $penalty_per) / 100;

            $start_date_obj->add($interval);
            $count++; //Count represents how many months are exceeded
        }
        $response['count_of_month'] = $count;

        //To check over due, if current date is greater than maturity minth, then i will be OD
        if ($current_date_obj > $end_date_obj) {
            $response['od'] = true;
        } else {
            $response['od'] = false;
        }

        //To check whether due has been nil with other charges

        $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id;");
        $row = $qry->fetch();

        $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);
        //if sum value is null or empty then assign 0 to it
        if ($row['penalty'] == '' or $row['penalty'] == null) {
            $row['penalty'] = 0;
        }
        if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
            $row['paid_amntpc'] = 0;
        }
        if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
            $row['waiver_amntpc'] = 0;
        }
        if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
            $row['coll_charge'] = 0;
        }
        if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
            $row['paid_amntcc'] = 0;
        }
        if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
            $row['waiver_amntcc'] = 0;
        }

        $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
        $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

        $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
        $row = $qry->fetch();

        if ($row['tot_amt_cal'] != '') {
            $total_for_nil = $row['tot_amt_cal'];
        } else {
            $total_for_nil = $row['principal_amt_cal'];
        }
        $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

        if ($due_nil_check == 0) {
            if ($curr_penalty > 0 || $curr_charges > 0) {
                $response['due_nil'] = true;
            }
        } else {
            $response['due_nil'] = false;
        }

        if ($count > 0) {

            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
            $response['penalty'] = intVal($penalty) - intVal($row['penalty']) - intVal($row['penalty_waiver']);

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
        } else {
            //If still current month is not ended, then pending will be same due amt
            $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = 0;
        }
    }
    if ($response['pending'] < 0) {
        $response['pending'] = 0;
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
function calculateInterestLoan($pdo, $loan_arr, $response, $cp_id)
{

    $due_start_from = $loan_arr['loan_date'];
    $maturity_month = $loan_arr['maturity_date'];

    //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
    $due_start_from = date('Y-m', strtotime($due_start_from));
    $maturity_month = date('Y-m', strtotime($maturity_month));

    // Create a DateTime object from the given date
    $maturity_month = new DateTime($maturity_month);
    // Subtract one month from the date
    $maturity_month->modify('-1 month');
    // Format the date as a string
    $maturity_month = $maturity_month->format('Y-m');

    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    $current_date = date('Y-m');

    $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
    $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
    $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

    $interval = new DateInterval('P1M'); // Create a one month interval
    if ($current_date_obj > $end_date_obj) {
        $res['od'] = true;
    } else {
        $res['od'] = false;
    }
    //condition start
    $count = 0;


    $qry = $pdo->query("SELECT  c.principal_waiver , c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
    FROM ( SELECT cus_profile_id, SUM(principal_waiver) AS principal_waiver,SUM(princ_amt_track) AS princ_amt_track 
    FROM collection 
    WHERE cus_profile_id = '$cp_id' ) c 
    LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt 
    FROM penalty_charges 
    WHERE cus_profile_id = '$cp_id' 
    GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
    LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt 
    FROM collection_charges 
    WHERE cus_profile_id = '$cp_id' 
    GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id;");

    $row = $qry->fetch();

    $tprincipal_paid =  intVal($row['princ_amt_track']) +  intVal($row['principal_waiver']);

    //if sum value is null or empty then assign 0 to it
    if ($row['penalty'] == '' or $row['penalty'] == null) {
        $row['penalty'] = 0;
    }
    if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
        $row['paid_amntpc'] = 0;
    }
    if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
        $row['waiver_amntpc'] = 0;
    }
    if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
        $row['coll_charge'] = 0;
    }
    if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
        $row['paid_amntcc'] = 0;
    }
    if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
        $row['waiver_amntcc'] = 0;
    }

    $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
    $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

    $total_for_nil = $response['total_amt'];

    $due_nil_check = intVal($total_for_nil) - intVal($tprincipal_paid);

    if ($due_nil_check == 0) {
        if ($curr_penalty > 0 || $curr_charges > 0) {
            $res['due_nil'] = true;
        } else {
            $res['due_nil'] = false;
        }
    } else {
        $res['due_nil'] = false;
    }
    while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {

        $start_date_obj->add($interval); //increase one month to loop again
        $count++; //Count represents how many months are exceeded
    }
    if ($start_date_obj >= $end_date_obj) {
        $count++; //because if the maturity date crossed the pending amount should have the maturity month's amount also so add 1month to count
    }
    $res['count_of_month'] = $count;

    if ($count > 0) {
        $interest_paid = getPaidInterest($pdo, $cp_id);

        $res['payable'] = payableCalculation($pdo, $loan_arr, $response, $cp_id) - $interest_paid;
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'curmonth', $cp_id) - $interest_paid;
        $res['pending'] = pendingCalculation($pdo, $loan_arr, $response, $cp_id) - $interest_paid;

        if ($res['pending'] < 0) {
            $res['pending'] = 0;
        }
        if ($res['payable'] < 0) {
            $res['payable'] = 0;
        }

        $res['penalty'] = getPenaltyCharges($pdo, $cp_id);
    } else {
        $interest_paid = getPaidInterest($pdo, $cp_id);
        //in this calculate till date Interest when month are not crossed for due starting month
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'forstartmonth', $cp_id) - $interest_paid;
        $res['pending'] = 0;
        $res['payable'] =  0;
        $res['penalty'] = 0;
    }

    $res['payable'] = ceilAmount($res['payable']);
    $res['pending'] = ceilAmount($res['pending']);
    $res['till_date_int'] = ceilAmount($res['till_date_int']);
    return $res;
}

function getTillDateInterest($loan_arr, $response, $pdo, $data, $cp_id)
{

    if ($data == 'forstartmonth') {

        //to calculate till date Interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {

            //get the loan isued month's date count
            $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

            //current month's total date
            $cur_date = new DateTime(date('Y-m-d'));

            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, '', $cp_id);
            // $result = (($issued_date->diff($cur_date))->days) * $issue_month_due;

            // Use your clean rounding logic
            $cur_amt = ceilAmount($result);

            $result = $cur_amt;
        }
        return $result;
    }
    if ($data == 'curmonth') {
        $cur_date = new DateTime(date('Y-m-d'));
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));


        $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'TDI', $cp_id);
        return $result;
    }
    if ($data == 'pendingmonth') {
        //for pending value check, goto 2 months before
        //bcoz last month value is on payable, till date int will be on cur date
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
        $cur_date = new DateTime(date('Y-m-d'));
        $cur_date->modify('-2 months');
        $cur_date->modify('last day of this month');
        $result = 0;

        if ($issued_date->format('Y-m') <= $cur_date->format('Y-m')) {
            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'pending', $cp_id);
        }
        return $result;
    }

    return $response;
}
function payableCalculation($pdo, $loan_arr, $response, $cp_id)
{
    $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
    $cur_date = new DateTime(date('Y-m-d'));
    $result = 0;

    if ($response['interest_calculate'] == "Month") {
        $last_month = clone $cur_date;
        $last_month->modify('-1 month'); // Last month same date
        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_month->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date; // Due to mutation in function

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);

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

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);
            $st_date->modify('+1 month');
            $st_date->modify('first day of this month');
        }
    }

    return $result;
}
function dueAmtCalculation($pdo, $start_date, $end_date, $due_amt, $loan_arr, $status, $cp_id)
{
    $start = new DateTime($start_date->format('Y-m-d'));
    $end = new DateTime($end_date->format('Y-m-d'));

    $interest_calculate = $loan_arr['interest_calculate'];
    $int_rate = $loan_arr['interest_rate'];
    $result = 0;
    $monthly_interest_data = [];

    $loanRow = $pdo->query("SELECT loan_amnt FROM loan_entry_loan_calculation WHERE cus_profile_id = '" . $cp_id . "'")->fetch(PDO::FETCH_ASSOC);
    $default_balance = $loanRow['loan_amnt'];

    $collections = $pdo->query("SELECT princ_amt_track,principal_waiver, coll_date FROM collection 
        WHERE cus_profile_id = '" . $cp_id . "' AND (princ_amt_track != '' OR principal_waiver != '')ORDER BY coll_date ASC")->fetchAll();

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
                $dueperday = $due_amt / intval($start->format('t'));

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
                $dueperday = $due_amt;
                $result += $dueperday;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $dueperday;

                $start->modify('+1 day');
            }
        }
    }

    // <------------------------------------------------------------------- Penalty Logic ----------------------------------------------------------------->

    if ($status === 'pending') {
        $penaltyRow = $pdo->query("SELECT penalty_type, overdue_penalty 
        FROM loan_category_creation 
        WHERE id = '" . $loan_arr['loan_category'] . "' ")->fetch(PDO::FETCH_ASSOC);

        $penalty_val  = $penaltyRow['overdue_penalty'] ?? 0;
        $penalty_type = strtolower(trim($penaltyRow['penalty_type'] ?? 'percentage'));

        $monthly_unpaid = [];
        $monthly_first_date = [];

        $current_month = date('Y-m'); // current month key

        foreach ($monthly_interest_data as $penalty_date => $cur_result) {
            $month_key = date('Y-m', strtotime($penalty_date));
            // ⛔ skip current month
            if ($month_key === $current_month) {
                continue;
            }

            $paid_interest   = getPaidInterest($pdo, $cp_id);
            $unpaid_interest = max(0, $cur_result - $paid_interest);

            if ($unpaid_interest > 0) {
                if (!isset($monthly_unpaid[$month_key])) {
                    $monthly_unpaid[$month_key] = 0;
                    $monthly_first_date[$month_key] = $penalty_date;
                }
                $monthly_unpaid[$month_key] += $unpaid_interest;
            }
        }

        // Step 2: Apply penalty only for past months
        foreach ($monthly_unpaid as $month => $unpaid) {
            if ($unpaid > 0 && $penalty_val > 0) {
                $penalty = ($penalty_type === 'rupee')
                    ? round($penalty_val)
                    : round(($unpaid * $penalty_val) / 100);

                $first_date = $monthly_first_date[$month];

                $checkPenalty = $pdo->query("SELECT 1 FROM penalty_charges 
                WHERE penalty_date = '$first_date' 
                AND cus_profile_id = '" . $cp_id . "'");

                if ($checkPenalty->rowCount() == 0) {
                    $pdo->query("INSERT INTO penalty_charges 
                    (cus_profile_id, penalty_date, penalty, created_date) 
                    VALUES ('$cp_id', '$first_date', $penalty, NOW())");
                }
            }
        }
    }


    return $result;
}
function pendingCalculation($pdo, $loan_arr, $response, $cp_id)
{
    $pending = getTillDateInterest($loan_arr, $response, $pdo, 'pendingmonth', $cp_id);
    return $pending;
}
function getPaidInterest($pdo, $cp_id)
{
    $qry = $pdo->query("SELECT COALESCE(SUM(int_amt_track), 0) + COALESCE(SUM(interest_waiver), 0) AS int_paid  FROM `collection` WHERE cus_profile_id = '$cp_id' and (int_amt_track != '' and int_amt_track IS NOT NULL OR interest_waiver != '' and interest_waiver IS NOT NULL) ");
    $int_paid = $qry->fetch()['int_paid'];
    return intVal($int_paid);
}
function getPenaltyCharges($pdo, $cp_id)
{
    // to get overall penalty paid till now to show pending penalty amount
    $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '$cp_id' ");
    $row = $result->fetch();
    if ($row['penalty'] == null) {
        $row['penalty'] = 0;
    }
    if ($row['penalty_waiver'] == null) {
        $row['penalty_waiver'] = 0;
    }
    //to get overall penalty raised till now for this req id
    $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '$cp_id' ");
    $row1 = $result1->fetch();
    if ($row1['penalty'] == null) {
        $penalty = 0;
    } else {
        $penalty = $row1['penalty'];
    }

    return $penalty - $row['penalty'] - $row['penalty_waiver'];
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
function checkStatusOfCustomer($response, $loan_arr, $cus_id, $pdo)
{

    if ($response) {
        for ($i = 0; $i < count($response['pending_customer']); $i++) {

            // $query = $pdo->query("SELECT cs.status AS cus_status FROM loan_issue li JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id  where li.cus_id = '$cus_id' and cs.status =7 ");
            // $row = $query->fetch();
            $curdate = date('Y-m-d');

            if (date('Y-m-d', strtotime($loan_arr['due_startdate'])) > date('Y-m-d', strtotime($curdate))) { //If the start date is on upcoming date then the sub status is current, until current date reach due_start_from date.
                $response['follow_cus_sts'] = 'Current';
            } else {
                if ($response['pending_customer'][$i] == true && $response['od_customer'][$i] == false) { //using i as 1 so subract it with 1

                    $response['follow_cus_sts'] = 'Pending';
                } else if ($response['od_customer'][$i] == true && $response['due_nil_customer'][$i] == false) {
                    $response['follow_cus_sts'] = 'OD';
                } elseif ($response['due_nil_customer'][$i] == true) {

                    $response['follow_cus_sts'] = 'Due Nil';
                } elseif ($response['pending_customer'][$i] == false) {

                    $response['follow_cus_sts'] = 'Current';
                }
            }
        }
        return $response['follow_cus_sts'];
    }
}


echo json_encode($response);
