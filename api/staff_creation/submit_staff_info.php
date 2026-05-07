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
$email = $_POST['email'];
$mobile1 = $_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$whatsapp = $_POST['whatsapp'];
$instagram = $_POST['instagram'];
$facebook = $_POST['facebook'];
$acc_holder_name = $_POST['acc_holder_name'];
$acc_number = $_POST['acc_number'];
$bank_name = $_POST['bank_name'];
$ifsc_code = $_POST['ifsc_code'];
$bank_branch = $_POST['bank_branch'];
$company_name = $_POST['company_name'];
$branch_name = $_POST['branch_name'];
$department = $_POST['department'];
$designation = $_POST['designation'];
$team = $_POST['team'];
$reporting_person = $_POST['reporting_person'];
$branch_admin = $_POST['branch_admin'];
$branch = $_POST['branch'];
$total_ctc = $_POST['total_ctc'];
$annual_ctc = $_POST['annual_ctc'];
$shift = $_POST['shift'];
$ot_payment = $_POST['ot_payment'];
$ot_per_hour = $_POST['ot_per_hour'];
$ot_per_day = $_POST['ot_per_day'];
$user_id = $_SESSION['user_id'];
$staff_profile_id = $_POST['staff_profile_id'];


$result = 0;
echo json_encode($result);
