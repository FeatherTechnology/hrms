<?php
require "../../ajaxconfig.php";

$loan_calc_id = $_POST['loan_calc_id'];
$result = array();

$qry = $pdo->query("SELECT lelc.loan_amount, cp.cus_limit  
    FROM loan_entry_loan_calculation lelc 
    JOIN customer_profile cp ON cp.id = lelc.cus_profile_id 
    WHERE lelc.id = '$loan_calc_id'");

if ($qry->rowCount() > 0) {
    $row = $qry->fetch();

    $loan_amount = $row['loan_amount'];
    $cus_limit   = $row['cus_limit'];

    if (empty($cus_limit)) {
        $result['status'] = 1; // Customer limit is empty
    } elseif ($cus_limit < $loan_amount) {
        $result['status'] = 2; // Customer limit is less than loan amount
    } else {
        $result['status'] = 3; // All good
        $result['cus_limit'] = $cus_limit; // Send limit back
    }
}

$pdo = null; // Close connection
echo json_encode($result);
