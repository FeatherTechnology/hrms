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
$reason = $_POST['reason'];

$entry_time = date('Y-m-d H:i:s', strtotime($_POST['entry_time']));


try {
    if ($att_id != '') {
        
        $qry = $pdo->query("UPDATE `attendance` SET `entry_time`='$entry_time',`updated_by`='$user_id',`reason`='$reason',`update_login_id`='$user_id',`updated_date`= now() WHERE id = $att_id ");

        if ($qry) {
            $result = '3';
        } else {
            $result = '4';
        }

    } else {

        $qry = $pdo->query("INSERT INTO `attendance`( `staff_profile_id`, `company_id`, `branch_id`, `dep_id`, `des_id`, `team_id`, `staff_type`, `entry_time`, `updated_by`, `reason`, `update_login_id`, `updated_date`) VALUES ('$stf_prf_id','$cmpy_id','$branch_id','$dep_id','$des_id ','$team_id','$staff_type','$entry_time','$user_id','$reason','$user_id',now())");

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
