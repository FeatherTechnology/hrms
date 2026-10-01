<?php
// Save attendance regularization details (Insert/Update).

require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];

$att_id = $_POST['att_id'];
$stf_prf_id = $_POST['stf_prf_id'];
$cmpy_id = $_POST['cmpy_id'];
$branch_id = $_POST['branch_id'];
$dep_id = $_POST['dep_id'];
$des_id = $_POST['des_id'];
$team_id = $_POST['team_id'];
$staff_type = $_POST['staff_type'];

if ($staff_type == 'Employer') {
    $staff_type = 1;
} elseif ($staff_type == 'Employee') {
    $staff_type = 2;
}

$deduction_amount = $_POST['deduction_amount'];
$reason = $_POST['reason'];

$entry_datetime = $_POST['entry_time'];
$exit_datetime = $_POST['exit_time'];

$updated_by      = !empty($entry_datetime) ? $user_id : '';
$updated_exit_by = !empty($exit_datetime) ? $user_id : '';

try {
    if ($att_id != '') {
        
        $qry = $pdo->query("UPDATE `attendance` SET `updated_time`='$entry_datetime',updated_exit_time='$exit_datetime',`updated_by`='$updated_by',`updated_exit_by`='$updated_exit_by',`reason`='$reason',`update_login_id`='$user_id',`updated_date`= now(),`deduction_amount`='$deduction_amount' WHERE id = $att_id ");

        if ($qry) {
            $result = '3';
        } else {
            $result = '4';
        }

    } else {

        $qry = $pdo->query("INSERT INTO `attendance`( `staff_profile_id`, `company_id`, `branch_id`, `dep_id`, `des_id`, `team_id`, `staff_type`, `updated_time`, `updated_exit_time`,`updated_by`,`updated_exit_by`, `deduction_amount`, `reason`, `update_login_id`, `updated_date`) VALUES ('$stf_prf_id','$cmpy_id','$branch_id','$dep_id','$des_id ','$team_id','$staff_type','$entry_datetime','$exit_datetime','$updated_by','$updated_exit_by','$deduction_amount','$reason','$user_id',now())");

        if ($qry) {
            $result = '1';
        } else {
            $result = '2';
        }
    }
} catch (Exception $e) {

    echo json_encode([
        'result' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

$pdo = null;

echo json_encode(['result' => $result]);
