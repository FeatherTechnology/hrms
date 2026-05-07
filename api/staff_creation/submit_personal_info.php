<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['pic']['name'])) {
    $path = "../../uploads/staff_creation/staff_pic/";
    $picture = $_FILES['pic']['name'];
    $pic_temp = $_FILES['pic']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = $_POST['per_pic'];
}
$staff_id = $_POST['staff_id'];
$staff_name = $_POST['staff_name'];
$staff_type = $_POST['staff_type'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$state = $_POST['state'];
$district = $_POST['district'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$dob = $_POST['dob'];
$blood_group = $_POST['blood_group'];
$gender = $_POST['gender'];
$marital_status = $_POST['marital_status'];
$spouse_name = $_POST['spouse_name'];
$anniversary_date = $_POST['anniversary_date'];
$joining_date = $_POST['joining_date'];
$relieve_date = $_POST['relieve_date'];
$notice_period = $_POST['notice_period'];
$pf_available = $_POST['pf_available'];
$esi_available = $_POST['esi_available'];
$pt_available = $_POST['pt_available'];
$user_id = $_SESSION['user_id'];
$staff_profile_id = $_POST['staff_profile_id'];


$result = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();

    $selectIC = $pdo->query("SELECT MAX(staff_id) as staff_id FROM staff_creation");
    $myStr = 'STF';
    $row = $selectIC->fetch();
    $ac2 = $row["staff_id"];

    if (!empty($ac2)) {
        $ac2 = $row["staff_id"];
        $number = explode('-', $ac2)[1];
        $new_number = (int)$number + 1;
        $appno2 = str_pad($new_number, 3, '0', STR_PAD_LEFT);
        $staff_id = $myStr . "-" . $appno2;
    } else {
        $initialapp = $myStr . "-001";
        $staff_id = $initialapp;
    }
    $qry = $pdo->query("INSERT INTO `staff_creation`(`staff_id`, `staff_name`, `staff_type`, `address`, `gender`, `state`, `district`, `place`, `pincode`, `dob`, `blood_group`, `marital_status`, `spouse_name`, `anniversary_date`, `joining_date`, `relieve_date`, `notice_period`, `pf_available`, `esi_available`, `pt_available`,`status`, `insert_login_id`, `created_on` ) VALUES ('$staff_id','$staff_name','$staff_type','$address','$gender','$state','$district','$place','$pincode','$dob','$blood_group','$marital_status','$spouse_name','$anniversary_date','$joining_date','$relieve_date','$notice_period','$pf_available','$esi_available','$pt_available','0','$user_id',CURRENT_TIMESTAMP())");
    if ($qry) {
        $result = 1; // Insert successful
    }
    $status = 0;
    $last_id = $pdo->lastInsertId();
    $result = array('result' => $result, 'last_id' => $last_id, 'pic' => $picture);
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
echo json_encode($result);
