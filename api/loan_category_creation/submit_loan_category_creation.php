<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$loan_category = $_POST['loan_category'];
$loan_limit = $_POST['loan_limit'];
$due_method = $_POST['due_method'];
$due_type = $_POST['due_type'];
$interest_calculate = $_POST['interest_calculate'];
$interest_rate_min = $_POST['interest_rate_min'];
$interest_rate_max = $_POST['interest_rate_max'];
$due_period_min = $_POST['due_period_min'];
$due_period_max = $_POST['due_period_max'];
$document_charge_type = $_POST['document_charge_type'];
$doc_charge_min = $_POST['doc_charge_min'];
$doc_charge_max = $_POST['doc_charge_max'];
$processing_fee_type = $_POST['processing_fee_type'];
$processing_fee_min = $_POST['processing_fee_min'];
$processing_fee_max = $_POST['processing_fee_max'];
$overdue_type = $_POST['overdue_type'];
$overdue_penalty = $_POST['overdue_penalty'];
$scheme_name = $_POST['scheme_name'];
$loan_cat_creation_id = $_POST['id'];

$result = 0;
if ($loan_cat_creation_id != '') {
    $qry = $pdo->query("UPDATE `loan_category_creation` SET `loan_category`='$loan_category',`loan_limit`='$loan_limit',`due_method`='$due_method',`due_type`='$due_type',`interest_calculate`='$interest_calculate',`interest_rate_min`='$interest_rate_min',`interest_rate_max`='$interest_rate_max',`due_period_min`='$due_period_min',`due_period_max`='$due_period_max',`document_charge_type`='$document_charge_type',`doc_charge_min`='$doc_charge_min',`doc_charge_max`='$doc_charge_max',`processing_fees_type`='$processing_fee_type',`processing_fee_min`='$processing_fee_min',`processing_fee_max`='$processing_fee_max',`penalty_type`='$overdue_type',`overdue_penalty`='$overdue_penalty',`scheme_name`='$scheme_name',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$loan_cat_creation_id'");
    if ($qry) {
        $result = 1; //Update
    }
} else {

    $qry = $pdo->query("INSERT INTO `loan_category_creation`(`loan_category`, `loan_limit`, `due_method`, `due_type`,`interest_calculate`, `interest_rate_min`, `interest_rate_max`, `due_period_min`, `due_period_max`,`document_charge_type`, `doc_charge_min`, `doc_charge_max`,`processing_fees_type`, `processing_fee_min`, `processing_fee_max`,`penalty_type`, `overdue_penalty`, `scheme_name`, `insert_login_id`,`created_on`) VALUES ('$loan_category','$loan_limit','$due_method','$due_type','$interest_calculate','$interest_rate_min','$interest_rate_max','$due_period_min','$due_period_max','$document_charge_type','$doc_charge_min','$doc_charge_max','$processing_fee_type','$processing_fee_min','$processing_fee_max','$overdue_type','$overdue_penalty','$scheme_name','$user_id',now())");
    if ($qry) {
        $result = 2; //Insert.
    }
}

echo json_encode($result);
