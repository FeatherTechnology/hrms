<?php
require "../../ajaxconfig.php";
session_start();

$user_id = $_SESSION['user_id'];

$hidden_id = $_POST['hidden_id'] ?? 0;

$stf_prf_id = $_POST['stf_prf_id'];
$cmpy_id = $_POST['cmpy_id'];
$branch_id = $_POST['branch_id'];
$dep_id = $_POST['dep_id'];
$des_id = $_POST['des_id'];
$team_id = $_POST['team_id'];
$req_type = $_POST['req_type'];
$leave_type = $_POST['leave_type'];
$leave_period = $_POST['leave_period'];
$balance_req = $_POST['balance_req'];
$current_month_ot_count = $_POST['current_month_ot_count'];
$total_min = $_POST['total_min'];
$staff_type = $_POST['staff_type'];

$req_date = !empty($_POST['req_date'])
    ? date('Y-m-d', strtotime($_POST['req_date']))
    : null;

$from_date = !empty($_POST['from_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['from_date']))
    : null;

$to_date = !empty($_POST['to_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['to_date']))
    : null;

$reason = $_POST['reason'] ?? '';
$remarks = $_POST['remarks'] ?? '';

/* ---------------- STATUS ---------------- */

if (!empty($_POST['approval_type'])) {
    $approval_type = $_POST['approval_type'];
} elseif ($staff_type == 1) {
    $approval_type = 1;
} else {
    $approval_type = 0;
}

try {

    if ($hidden_id > 0) {

        $sql = "UPDATE regularization SET 
            remarks = '$remarks',
            status = '$approval_type',
            updated_login_id = '$user_id',
            updated_date = NOW()
        WHERE id = $hidden_id";

        $qry = $pdo->query($sql);

        $result = $qry ? '1' : '2';

    } else {

        $sql = "INSERT INTO regularization (
            staff_profile_id, company_id, branch_id, dep_id, des_id, team_id,
            req_type, leave_type,leave_period, balance_req, current_month_ot_count, req_date,
            from_date, to_date, total_min,
            reason, status, insert_login_id, created_date
        ) VALUES (
            '$stf_prf_id', '$cmpy_id', '$branch_id', '$dep_id', '$des_id', '$team_id',
            '$req_type', '$leave_type', '$leave_period','$balance_req', '$current_month_ot_count', '$req_date',
            '$from_date', '$to_date', '$total_min',
            '$reason', '$approval_type', '$user_id', NOW()
        )";

        $qry = $pdo->query($sql);

        $result = $qry ? '3' : '4';
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