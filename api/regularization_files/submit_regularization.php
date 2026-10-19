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
$ave_balance = $_POST['ave_balance'];
$shift_start = $_POST['shift_start'];
$shift_end = $_POST['shift_end'];
$leave_period = $_POST['leave_period'];
$balance_req = $_POST['balance_req'];
$current_month_ot_count = $_POST['current_month_ot_count'];
$cur_month_lop_count = $_POST['cur_month_lop_count'];
$total_min = $_POST['total_min'];
$leave_days = $_POST['leave_days'];
$staff_type = $_POST['staff_type'];
$permission_type = $_POST['permission_type'];
if ($req_type == '4') {
    $ot_payment = $_POST['ot_payment'];
} else {
    $ot_payment = '';
}


$req_date = !empty($_POST['req_date'])
    ? date('Y-m-d', strtotime($_POST['req_date']))
    : null;

$from_date = !empty($_POST['from_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['from_date']))
    : null;

$to_date = !empty($_POST['to_date'])
    ? date('Y-m-d H:i:s', strtotime($_POST['to_date']))
    : null;

$purpose = $_POST['purpose'] ?? '';
$remarks = $_POST['remarks'] ?? '';

/* ---------------- STATUS ---------------- */

if (!empty($_POST['approval_type'])) {
    $approval_type = $_POST['approval_type'];
} elseif ($staff_type == 1) {
    $approval_type = 1;
} else {
    $approval_type = 0;
}
if (!empty($_FILES['upload']['name'])) {
    $path = "../../uploads/leave/document/";
    $pic = $_FILES['upload']['name'];
    $pic_temp = $_FILES['upload']['tmp_name'];
    $picfolder = $path . $pic;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $pic = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $pic)) {
        //this loop will continue until it generates a unique file name
        $pic = uniqid() . '.' . $fileExtension;
    }

    move_uploaded_file($pic_temp, $path . $pic);
} else {
    $pic = $_POST['doc_upload'];
}
try {

    if ($hidden_id > 0) {

        $sql = "UPDATE regularization SET 
           from_date = '$from_date',
           to_date = '$to_date',
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
            req_type, permission_type, shift_start ,shift_end ,leave_type, ave_balance, leave_period, balance_req, current_month_ot_count,ot_payment, req_date,
            from_date, to_date, total_min,leave_days,cur_month_lop_count,
            purpose,upload, status, insert_login_id, created_date
        ) VALUES (
            '$stf_prf_id', '$cmpy_id', '$branch_id', '$dep_id', '$des_id', '$team_id',
            '$req_type','$permission_type','$shift_start','$shift_end', '$leave_type','$ave_balance', '$leave_period','$balance_req', '$current_month_ot_count','$ot_payment','$req_date',
            '$from_date', '$to_date', '$total_min','$leave_days','$cur_month_lop_count',
            '$purpose','$pic','$approval_type', '$user_id', NOW()
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
