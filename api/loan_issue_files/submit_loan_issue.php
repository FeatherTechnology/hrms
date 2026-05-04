<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cus_id = $_POST['cus_id'];
$cus_profile_id = $_POST['cus_profile_id'];
$loan_amnt = $_POST['loan_amnt'];
$due_startdate = $_POST['due_startdate'];
$maturity_date = $_POST['maturity_date'];
$net_cash = $_POST['net_cash'];
$bal_net_cash = $_POST['bal_net_cash'];
$bal_amount = !empty($_POST['bal_amount']) ? $_POST['bal_amount'] : 0;
$payment_type = $_POST['payment_type'];
$payment_mode = $_POST['payment_mode'];
$bank_name = $_POST['bank_name'];
$transaction_id = $_POST['transaction_id'];
$chequeno = $_POST['chequeno'];
$cash = $_POST['cash'];
$chequeValue = $_POST['chequeValue'];
$chequeRemark = $_POST['chequeRemark'];
$transaction_remark = $_POST['transaction_remark'];
$transaction_value = $_POST['transaction_value'];

$issue_date = $_POST['issue_date'];
$issue_person = $_POST['issue_person'];
$issue_relationship = $_POST['issue_relationship'];
if (isset($_POST['bank_clr_bank_id'])) {
    $bank_clr_bank_id = $_POST['bank_clr_bank_id'];
}
if (isset($_POST['bank_clr_trans_amnt'])) {
    $bank_clr_trans_amnt = $_POST['bank_clr_trans_amnt'];
}

$qry = $pdo->query("INSERT INTO `loan_issue`(`cus_id`, `cus_profile_id`, `loan_amnt`, `net_cash`,`net_bal_cash`,`payment_type`, `payment_mode`, `bank_name`,`cash`,`cheque_val`,`transaction_val`, `transaction_id`, `cheque_no`,`cheque_remark`,`tran_remark`,`balance_amount`, `issue_date`, `issue_person`, `relationship`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$cus_profile_id','$loan_amnt','$net_cash','$bal_net_cash','$payment_type','$payment_mode','$bank_name','$cash','$chequeValue','$transaction_value','$transaction_id','$chequeno','$chequeRemark','$transaction_remark','$bal_amount','$issue_date','$issue_person','$issue_relationship','$user_id',now())");

$qry2 = $pdo->query("UPDATE `loan_entry_loan_calculation` SET `due_startdate`='$due_startdate',`maturity_date`='$maturity_date',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cus_profile_id' ");
if ($payment_type == "1") {
    // Check if balance_amount is zero
    if ($bal_amount == 0) {
$qry3 = $pdo->query("UPDATE `customer_status` SET `status`='7',`coll_status`='Current',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cus_profile_id' "); //Loan Issued.
    }
}else if ($payment_type == "2"){
    $qry3 = $pdo->query("UPDATE `customer_status` SET `status`='7',`coll_status`='Current',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cus_profile_id' "); //Loan Issued.
}
if (!empty($bank_clr_bank_id)) {
        // Deduct paid amount
        $amnt = floatval($transaction_value);
        $bank_clr_trans_amnt -= $amnt;

        // Prevent negative balance
        if ($bank_clr_trans_amnt < 0) {
            $bank_clr_trans_amnt = 0;
        }

        $clr_sts = ($bank_clr_trans_amnt == 0) ? 1 : 0; //1 - cleared, 0 - uncleared
        $query = $pdo->prepare("UPDATE bank_clearance SET transaction_amount = ?, clr_status = ? WHERE id = ? ");

        $query->execute([$bank_clr_trans_amnt, $clr_sts, $bank_clr_bank_id]);
        
        $historyStmt = $pdo->prepare("INSERT INTO cleared_bank_stmt_history
            (bank_stmt_id, transaction_amount, type, screens, insert_login_id, created_date)
            VALUES
            (:bank_stmt_id, :amt, 2, 'Loan Issued', :user_id, NOW()) ");

        $historyStmt->execute([
            ':bank_stmt_id' => $bank_clr_bank_id,
            ':amt' => $amnt,
            ':user_id' => $user_id
        ]);
    }

// $qry = $pdo->query("SELECT cus_name, mobile1 FROM `customer_profile` WHERE `id` = '$cus_profile_id' ");
// $row = $qry->fetch_assoc();
// $customer_name = $row['cus_name'];
// $cus_mobile1 = $row['mobile1'];

// $message = "";
// $templateid	= ''; //FROM DLT PORTAL.
// // Account details
// $apiKey = '';
// // Message details
// $sender = '';
// // Prepare data for POST request
// $data = 'access_token='.$apiKey.'&to='.$cus_mobile1.'&message='.$message.'&service=T&sender='.$sender.'&template_id='.$templateid;
// // Send the GET request with cURL
// $url = 'https://sms.messagewall.in/api/v2/sms/send?'.$data; 
// $response = file_get_contents($url);  
// // Process your response here
// return $response; 

if ($qry) {
    $result = 1;
} else {
    $result = 0;
}

echo json_encode($result);
