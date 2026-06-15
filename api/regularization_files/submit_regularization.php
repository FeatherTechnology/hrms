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
$balance_req = $_POST['balance_req'];
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
$app_total_min = $_POST['app_total_min'] ?? null;

/* ---------------- APPROVED DATES ---------------- */

$app_from_date = !empty($_POST['app_from_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['app_from_date']))
    : null;

$app_to_date = !empty($_POST['app_to_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['app_to_date']))
    : null;

/* ---------------- STATUS ---------------- */

if (!empty($_POST['approval_type'])) {
    $approval_type = $_POST['approval_type'];
} elseif ($staff_type == 1) {
    $app_from_date = $from_date;
    $app_to_date = $to_date;
    $app_total_min = $total_min;
    $approval_type = 1;
} else {
    $approval_type = 0;
}

/* ---------------- CONVERT NULL FOR SQL ---------------- */

$app_from_sql = $app_from_date ? "'$app_from_date'" : "NULL";
$app_to_sql   = $app_to_date ? "'$app_to_date'" : "NULL";
$app_min_sql  = $app_total_min !== null ? "'$app_total_min'" : "NULL";

try {

    if ($hidden_id > 0) {

        $sql = "UPDATE regularization SET 
            approved_from_date = $app_from_sql,
            approved_to_date = $app_to_sql,
            approved_total_min = $app_min_sql,
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
            req_type, leave_type, balance_req, req_date,
            from_date, to_date, total_min,
            approved_from_date, approved_to_date, approved_total_min,
            reason, status, insert_login_id, created_date
        ) VALUES (
            '$stf_prf_id', '$cmpy_id', '$branch_id', '$dep_id', '$des_id', '$team_id',
            '$req_type', '$leave_type', '$balance_req', '$req_date',
            '$from_date', '$to_date', '$total_min',
            $app_from_sql, $app_to_sql, $app_min_sql,
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