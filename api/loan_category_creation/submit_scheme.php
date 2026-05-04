<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$add_scheme_name = $_POST['addSchemeName'];
$scheme_due_method = $_POST['schemeDueMethod'];
$profit_method = $_POST['profitMethod'];
$scheme_interest_rate = $_POST['schemeInterestRate'];
$scheme_due_period = $_POST['schemeDuePeriod'];
$scheme_overdue_penalty = $_POST['schemeOverduePenalty'];
$doc_charge_type = $_POST['docChargeType'];
$scheme_doc_charge_min = $_POST['schemeDocChargeMin'];
$scheme_doc_charge_max = $_POST['schemeDocChargeMax'];
$processing_fee_type = $_POST['processingFeeType'];
$scheme_processing_fee_min = $_POST['schemeProcessingFeeMin'];
$scheme_processing_fee_max = $_POST['schemeProcessingFeeMax'];
$id = $_POST['id'];

$result = '0'; //initial
if ($id != '0' && $id != '') {
    $qry = $pdo->query("UPDATE `scheme` SET `scheme_name`='$add_scheme_name',`due_method`='$scheme_due_method',`profit_method`='$profit_method',`interest_rate_percent`='$scheme_interest_rate',`due_period_percent`='$scheme_due_period',`overdue_penalty_percent`='$scheme_overdue_penalty',`doc_charge_type`='$doc_charge_type',`doc_charge_min`='$scheme_doc_charge_min',`doc_charge_max`='$scheme_doc_charge_max',`processing_fee_type`='$processing_fee_type',`processing_fee_min`='$scheme_processing_fee_min',`processing_fee_max`='$scheme_processing_fee_max',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
    if ($qry) {
        $result = '1'; //Update
    }
} else {
    $qry = $pdo->query("SELECT * FROM `scheme` WHERE REPLACE(TRIM(scheme_name), ' ', '') = REPLACE(TRIM('$add_scheme_name'), ' ', '') ");
    if ($qry->rowCount() > 0) {
        $result = '3'; //already Exists.
    } else {
        $qry = $pdo->query("INSERT INTO `scheme`(`scheme_name`, `due_method`, `profit_method`, `interest_rate_percent`, `due_period_percent`, `overdue_penalty_percent`, `doc_charge_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_type`, `processing_fee_min`, `processing_fee_max`, `insert_login_id`, `created_on`) VALUES ('$add_scheme_name','$scheme_due_method','$profit_method','$scheme_interest_rate','$scheme_due_period','$scheme_overdue_penalty','$doc_charge_type','$scheme_doc_charge_min','$scheme_doc_charge_max','$processing_fee_type','$scheme_processing_fee_min','$scheme_processing_fee_max','$user_id',now())");
        if ($qry) {
            $result = '2'; //Insert.
        }
    }
}
echo json_encode($result);
