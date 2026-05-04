<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cp_id = $_POST['cp_id'];
$cus_id = $_POST['cus_id'];
$cus_name = $_POST['cus_name'];
$area_id = $_POST['area_id'];
$branch_id = $_POST['branch_id'];
$line_id = $_POST['line_id'];
$loan_category_id = $_POST['loan_category_id'];
$status = $_POST['status'];
$sub_status = $_POST['sub_status'];
$tot_amt = $_POST['tot_amt'];
$paid_amt = $_POST['paid_amt'];
$bal_amt = $_POST['bal_amt'];
$due_amt = $_POST['due_amt'];
$pending_amt = $_POST['pending_amt'];
$payable_amt = $_POST['payable_amt'];
$penalty = $_POST['penalty'];
$coll_charge = $_POST['coll_charge'];
$due_amt_track = $_POST['due_amt_track'];
$princ_amt_track = $_POST['princ_amt_track'];
$int_amt_track = $_POST['int_amt_track'];
$penalty_track = $_POST['penalty_track'];
$coll_charge_track = $_POST['coll_charge_track'];
$total_paid_track = $_POST['total_paid_track'];
$pre_close_waiver = $_POST['pre_close_waiver'];
if (isset($_POST['interest_waiver'])) {
    $interest_waiver = $_POST['interest_waiver'];
}
if (isset($_POST['principal_waiver'])) {
    $principal_waiver = $_POST['principal_waiver'];
}
$penalty_waiver = $_POST['penalty_waiver'];
$coll_charge_waiver = $_POST['coll_charge_waiver'];
$till_date_int = $_POST['till_date_int'];
$total_waiver = $_POST['total_waiver'];
$collection_date = date('Y-m-d', strtotime($_POST['collection_date']));
$collection_id = $_POST['collection_id'];
$collection_mode = $_POST['collection_mode'];
$bank_id = $_POST['bank_id'];
$cheque_no = $_POST['cheque_no'];
$trans_id = $_POST['trans_id'];
$trans_date = ($_POST['trans_date'] !='') ? $_POST['trans_date'] : '0000-00-00';
if (isset($_POST['bank_clr_bank_id'])) {
    $bank_clr_bank_id = $_POST['bank_clr_bank_id'];
}
if (isset($_POST['bank_clr_trans_amnt'])) {
    $bank_clr_trans_amnt = $_POST['bank_clr_trans_amnt'];
}
try {

    // Begin transaction
    $pdo->beginTransaction();

    $myStr = 'COL';
    $selectIC = $pdo->query("SELECT coll_code FROM `collection` WHERE coll_code != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");

    if ($selectIC->rowCount() > 0) {
        $row = $selectIC->fetch();
        $ac2 = $row["coll_code"];
        $appno2 = ltrim(strstr($ac2, '-'), '-');
        $appno2 = intval($appno2) + 1;
        $collection_id = $myStr . "-" . $appno2;
    } else {
        $collection_id = $myStr . "-101";
    }

$qry = $pdo->query("INSERT INTO `collection`( `coll_code`, `cus_profile_id`, `cus_id`, `cus_name`, `branch`, `area`, `line`, `loan_category`, `coll_status`, `coll_sub_status`, `tot_amt`, `paid_amt`, `bal_amt`, `due_amt`, `pending_amt`, `payable_amt`, `till_date_int`,`penalty`, `coll_charge`, `coll_mode`, `bank_id`, `cheque_no`, `trans_id`, `trans_date`, `coll_date`, `due_amt_track`, `princ_amt_track`, `int_amt_track`, `penalty_track`, `coll_charge_track`, `total_paid_track`, `pre_close_waiver`,`principal_waiver`,`interest_waiver`, `penalty_waiver`, `coll_charge_waiver`, `total_waiver`, `insert_login_id`, `created_date`) VALUES ('$collection_id','$cp_id','$cus_id','$cus_name','$branch_id','$area_id','$line_id','$loan_category_id','$status','$sub_status','$tot_amt','$paid_amt','$bal_amt','$due_amt','$pending_amt','$payable_amt','$till_date_int','$penalty','$coll_charge','$collection_mode','$bank_id','$cheque_no','$trans_id','$trans_date','".$collection_date.' '.date('H:i:s')."','$due_amt_track','$princ_amt_track','$int_amt_track','$penalty_track','$coll_charge_track','$total_paid_track','$pre_close_waiver','$principal_waiver','$interest_waiver','$penalty_waiver','$coll_charge_waiver','$total_waiver','$user_id',current_timestamp )");

// $qry = $pdo->query("UPDATE `customer_status` SET `coll_status`='$sub_status',`updated_on`='current_timestamp' WHERE cus_profile_id='$cp_id' ");

if ($qry) {
    $coll_id = $collection_id;
    $result = '1';
} else {
    $result = '2';
}

if (($penalty_track != '' AND $penalty_track >0) or ($penalty_waiver != '' AND $penalty_waiver >0)) {
    $qry1 = $pdo->query("INSERT INTO `penalty_charges`(`cus_profile_id`, `paid_date`, `paid_amnt`, `waiver_amnt`, `created_date`) VALUES ('$cp_id','$collection_date','$penalty_track','$penalty_waiver', current_timestamp) ");
}

if ($coll_charge_track != '' or $coll_charge_waiver != '') {
    $qry2 = $pdo->query("INSERT INTO `collection_charges`(`cus_profile_id`, `paid_date`, `paid_amnt`, `waiver_amnt`) VALUES ('$cp_id','$collection_date','$coll_charge_track','$coll_charge_waiver')");
}

if($cheque_no != ''){
    $qry = $pdo->query("UPDATE `cheque_no_list` SET `used_status`='1' WHERE `id`=$cheque_no "); //If cheque has been used change status to 1
}

$check = intval($due_amt_track) + intval($pre_close_waiver) - intval($bal_amt);
 $till_now_interest_check = 0;
if (($princ_amt_track != '' or $int_amt_track != '') and ($due_amt_track == '' or $due_amt_track == 0 or $due_amt_track == null)) {
    // if this condition is true then it will be the interest based loan. coz thats where we able to give princ/int amt track and not able to give due amt track
    //if yes then $check variable should check with principal amt
    $check = intVal($princ_amt_track)  + intVal($principal_waiver) - intval($bal_amt);
    $till_now_interest_check = intval($int_amt_track) + intval($interest_waiver) - intval($till_date_int);
}


$penalty_check = intval($penalty_track) + intval($penalty_waiver) - intval($penalty);
$coll_charge_check = intval($coll_charge_track) + intval($coll_charge_waiver) - intval($coll_charge);

if ($check == 0 && $penalty_check == 0 && $coll_charge_check == 0 && $till_now_interest_check == 0) {
    $closedQry = $pdo->query("UPDATE `customer_status` SET `coll_status`='Closed', `status`='8', `in_closed_date`=now(), `update_login_id`='$user_id', `updated_on`=now() WHERE `cus_profile_id`='$cp_id' "); //balance is zero change the customer status as 8, moved to closed.
    if ($closedQry) {
        $result = '3';
    }
}

    if (!empty($bank_clr_bank_id)) {
        // Deduct paid amount
        $bank_clr_trans_amnt -= $total_paid_track;

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
            (:bank_stmt_id, :amt, 1, 'Collection', :user_id, NOW()) ");

        $historyStmt->execute([
            ':bank_stmt_id' => $bank_clr_bank_id,
            ':amt' => $total_paid_track,
            ':user_id' => $user_id
        ]);
    }
// $qry = $pdo->query("SELECT cus_name, mobile1 FROM `customer_profile` WHERE `id` = '$cp_id' ");
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
    $pdo->commit(); //  Commit
    
} catch (Exception $e) {
    $pdo->rollBack(); //  Rollback on error
    echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    exit;
}
$pdo = null;
echo json_encode(['result' => $result, 'coll_id' => $coll_id]); // Return collection ID in response
