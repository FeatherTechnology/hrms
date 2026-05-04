<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$customer_profile_id = $_POST['customer_profile_id'];
$cus_id = $_POST['cus_id'];
$loan_id_calc = $_POST['loan_id_calc'];
$loan_category_calc = $_POST['loan_category_calc'];
$category_info_calc = $_POST['category_info_calc'];
$loan_amount_calc = $_POST['loan_amount_calc'];
$profit_type_calc = $_POST['profit_type_calc'];
$due_method_calc = $_POST['due_method_calc'];
$due_type_calc = $_POST['due_type_calc'];
$interest_calculate = $_POST['interest_calculate'];
$profit_method_calc = $_POST['profit_method_calc'];
$scheme_due_method_calc = $_POST['scheme_due_method_calc'];
$scheme_day_calc = $_POST['scheme_day_calc'];
$scheme_name_calc = $_POST['scheme_name_calc'];
$interest_rate_calc = $_POST['interest_rate_calc'];
$due_period_calc = $_POST['due_period_calc'];
$doc_charge_calc = $_POST['doc_charge_calc'];
$processing_fees_calc = $_POST['processing_fees_calc'];
$loan_amnt_calc = $_POST['loan_amnt_calc'];
$principal_amnt_calc = $_POST['principal_amnt_calc'];
$interest_amnt_calc = $_POST['interest_amnt_calc'];
$total_amnt_calc = $_POST['total_amnt_calc'];
$due_amnt_calc = $_POST['due_amnt_calc'];
$doc_charge_calculate = $_POST['doc_charge_calculate'];
$processing_fees_calculate = $_POST['processing_fees_calculate'];
$net_cash_calc = $_POST['net_cash_calc'];
$loan_date_calc = $_POST['loan_date_calc'];
$due_startdate_calc = $_POST['due_startdate_calc'];
$maturity_date_calc = $_POST['maturity_date_calc'];
$referred_calc = $_POST['referred_calc'];
$agent_id_calc = $_POST['agent_id_calc'];
$agent_name_calc = $_POST['agent_name_calc'];
$id = $_POST['id'];
$cus_status = $_POST['cus_status'];
if ($due_type_calc == '') {
    $due_type_calc = 'EMI';
}
if ($profit_type_calc == '1') {
    $due_method_calc = '';
}
if ($due_type_calc == 'Interest') {
    $total_amnt_calc = '';
    $due_amnt_calc = '';
} else {
    $interest_calculate = '';
}

$status = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest Branch code
    $selectIC = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE loan_id != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");
    if ($id == '') {
        $myStr = "LID";
        if ($selectIC->rowCount() > 0) {
            $row = $selectIC->fetch();
            $ac2 = $row["loan_id"];
            $appno2 = ltrim(strstr($ac2, '-'), '-');
            $appno2 = $appno2 + 1;
            $loan_id_calc = $myStr . "-" . $appno2;
        } else {
            $initialapp = $myStr . "-101";
            $loan_id_calc = $initialapp;
        }
        $qry = $pdo->query("INSERT INTO `loan_entry_loan_calculation`(`cus_profile_id`, `cus_id`, `loan_id`, `loan_category`, `category_info`, `loan_amount`, `profit_type`, `due_method`, `due_type`,`interest_calculate`, `profit_method`, `scheme_due_method`, `scheme_day`, `scheme_name`, `interest_rate`, `due_period`, `doc_charge`, `processing_fees`, `loan_amnt`, `principal_amnt`, `interest_amnt`, `total_amnt`, `due_amnt`, `doc_charge_calculate`, `processing_fees_calculate`, `net_cash`, `loan_date`, `due_startdate`, `maturity_date`, `referred`, `agent_id`, `agent_name`, `insert_login_id`, `created_on`) 
    VALUES ('$customer_profile_id', '$cus_id', '$loan_id_calc','$loan_category_calc','$category_info_calc','$loan_amount_calc','$profit_type_calc','$due_method_calc','$due_type_calc','$interest_calculate','$profit_method_calc','$scheme_due_method_calc','$scheme_day_calc','$scheme_name_calc','$interest_rate_calc','$due_period_calc','$doc_charge_calc','$processing_fees_calc','$loan_amnt_calc','$principal_amnt_calc','$interest_amnt_calc','$total_amnt_calc','$due_amnt_calc','$doc_charge_calculate','$processing_fees_calculate','$net_cash_calc','$loan_date_calc','$due_startdate_calc','$maturity_date_calc','$referred_calc','$agent_id_calc','$agent_name_calc','$user_id',now())");
        if ($qry) {
            $status = 1;
            $last_id = $pdo->lastInsertId();
        }
    } else {
        $qry = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `cus_profile_id`='$customer_profile_id', `cus_id` = '$cus_id', `loan_id`='$loan_id_calc',`loan_category`='$loan_category_calc',`category_info`='$category_info_calc',`loan_amount`='$loan_amount_calc',`profit_type`='$profit_type_calc',`due_method`='$due_method_calc',`due_type`='$due_type_calc',`interest_calculate`='$interest_calculate',`profit_method`='$profit_method_calc',`scheme_due_method`='$scheme_due_method_calc',`scheme_day`='$scheme_day_calc',`scheme_name`='$scheme_name_calc',`interest_rate`='$interest_rate_calc',`due_period`='$due_period_calc',`doc_charge`='$doc_charge_calc',`processing_fees`='$processing_fees_calc',`loan_amnt`='$loan_amnt_calc',`principal_amnt`='$principal_amnt_calc',`interest_amnt`='$interest_amnt_calc',`total_amnt`='$total_amnt_calc',`due_amnt`='$due_amnt_calc',`doc_charge_calculate`='$doc_charge_calculate',`processing_fees_calculate`='$processing_fees_calculate',`net_cash`='$net_cash_calc',`loan_date`='$loan_date_calc',`due_startdate`='$due_startdate_calc',`maturity_date`='$maturity_date_calc',`referred`='$referred_calc',`agent_id`='$agent_id_calc',`agent_name`='$agent_name_calc',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
        if ($qry) {
            $status = 2;
            $last_id = $id;
        }
    }
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}

$qry = $pdo->query("UPDATE `customer_status` SET `loan_calculation_id`='$last_id', `status`='$cus_status', `update_login_id`='$user_id', `updated_on`=now() WHERE `cus_profile_id`='$customer_profile_id'  AND status=1 ");
$pdo = null; // Close Connection

$result = array('status' => $status, 'last_id' => $last_id);
echo json_encode($result);
