<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];

$hidden_id = $_POST['hidden_id'];
$stf_prf_id = $_POST['stf_prf_id'];
$cmpy_id = $_POST['cmpy_id'];
$branch_id = $_POST['branch_id'];
$dep_id = $_POST['dep_id'];
$des_id = $_POST['des_id'];
$team_id = $_POST['team_id'];
$req_type = $_POST['req_type'];
$leave_type = $_POST['leave_type'];
$balance_req = $_POST['balance_req'];
$total_min = $_POST['total_min'];
$req_date = date('Y-m-d', strtotime($_POST['req_date']));
$from_date = date('Y-m-d H:i:s', strtotime($_POST['from_date']));
$to_date = date('Y-m-d H:i:s', strtotime($_POST['to_date']));

$reason = $_POST['reason'];



$app_from_date =  date('Y-m-d H:i:s', strtotime($_POST['app_from_date']));
$app_to_date =  date('Y-m-d H:i:s', strtotime($_POST['app_to_date']));

$remarks = $_POST['remarks'];
$app_total_min = $_POST['app_total_min'];
if (isset($_POST['approval_type']) && !empty($_POST['approval_type'])) {
    $approval_type = $_POST['approval_type'];
} else {
    $approval_type = 0;
}


try {
    if ($hidden_id > 0) {
        $qry = $pdo->query("UPDATE `regularization` SET `approved_from_date`='$app_from_date',`approved_to_date`='$app_to_date ',`approved_total_min`='$app_total_min',`remarks`='$remarks',`status`='$approval_type',`updated_login_id`='$user_id ',`updated_date`=now() WHERE id = $hidden_id ");

        if ($qry) {
            $result = '1';
        } else {
            $result = '2';
        }
    } else {
        $qry = $pdo->query("INSERT INTO `regularization` (`staff_profile_id`, `company_id`, `branch_id`, `dep_id`, `des_id`, `team_id`, `req_type`, `leave_type`,  `balance_req`, `req_date`, `from_date`, `to_date`,`total_min`, `reason`, `status`, `insert_login_id`, `created_date`)
        VALUES
        ('$stf_prf_id','$cmpy_id','$branch_id','$dep_id', '$des_id','$team_id','$req_type','$leave_type', '$balance_req','$req_date','$from_date','$to_date','$total_min','$reason','$approval_type',$user_id,now())");

        if ($qry) {
            $result = '3';
        } else {
            $result = '4';
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
