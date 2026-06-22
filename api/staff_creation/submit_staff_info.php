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
$staff_type = 2;
$address = $_POST['address'];
$gender = $_POST['gender'];
$state = $_POST['state'];
$district = $_POST['district'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$dob = $_POST['dob'];
$age = $_POST['age'];
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
$reporting_person_type = (strtolower($_POST['reporting_person_type']) == 'director') ? 1 : 2;
$branch_admin = $_POST['branch_admin'];
$branch = $_POST['branch'];
$off_type = $_POST['off_type'];
$total_ctc = $_POST['total_ctc'];
$annual_ctc = $_POST['annual_ctc'];
$shift = $_POST['shift'];
$ot_payment = $_POST['ot_payment'];
$ot_per_hour = $_POST['ot_per_hour'];
$ot_per_day = $_POST['ot_per_day'];
$user_id = $_SESSION['user_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$total_amount = $_POST['total_ctc_amount'];
$ctcDetails = json_decode($_POST['ctcDetails'], true);

 $qry = $pdo->query("UPDATE `staff_creation` SET `staff_name`='$staff_name',`staff_type`='$staff_type',`address`='$address',`state`='$state',`district`='$district',`place`='$place',`pincode`='$pincode',`dob`='$dob',`age`='$age',`blood_group`='$blood_group',`pic`='$picture',`gender`='$gender',`marital_status`='$marital_status',`spouse_name`='$spouse_name',`anniversary_date`='$anniversary_date',`joining_date`='$joining_date',`relieve_date`='$relieve_date',`notice_period`='$notice_period',`email`='$email',`mobile1`='$mobile1',`mobile2`='$mobile2',`whatsapp`='$whatsapp',`instagram`='$instagram',`facebook`='$facebook',`acc_holder_name`='$acc_holder_name',`acc_number`='$acc_number',`bank_name`='$bank_name',`bank_branch`='$bank_branch',`ifsc_code`='$ifsc_code',`status`=1,`update_login_id`='$user_id', `updated_on`=NOW() WHERE `id`= '$staff_profile_id'");

/* ===============================
   OCCUPATION INFO
   INSERT ONLY ONE TIME
=================================*/
$check = $pdo->query("SELECT id FROM occupation_info 
WHERE staff_profile_id='$staff_profile_id'");

if ($check->rowCount() == 0) {

    $pdo->query("INSERT INTO occupation_info SET
        staff_profile_id='$staff_profile_id',
        staff_id='$staff_id',
        company_id='$company_name',
        branch_id='$branch_name',
        department='$department',
        team='$team',
        designation='$designation',
        off_type='$off_type',
        branch_admin='$branch_admin',
        reporting_person='$reporting_person',
        reporting_person_type='$reporting_person_type',
        branch='$branch',
        pf_available='$pf_available',
        esi_available='$esi_available',
        pt_available='$pt_available',
        total_ctc='$total_ctc',
        annual_ctc='$annual_ctc',
        shift='$shift',
        ot_payment='$ot_payment',
        ot_per_hour='$ot_per_hour',
        ot_per_day='$ot_per_day',
        insert_login_id='$user_id',
        created_on=NOW()
    ");
}

/* ===============================
   CTC INFO
   INSERT ONLY ONE TIME
=================================*/
$checkCTC = $pdo->query("SELECT id FROM staff_ctc_info 
WHERE staff_profile_id='$staff_profile_id'");

if ($checkCTC->rowCount() == 0) {

    foreach ($ctcDetails as $row) {

        $ctc_id         = $row['ctc_id'];
        $ctc_amount     = $row['ctc_amount'];
        $ctc_percentage = $row['ctc_percentage'];

        $pdo->query("INSERT INTO staff_ctc_info SET
            staff_profile_id='$staff_profile_id',
            staff_id='$staff_id',
            ctc_id='$ctc_id',
            ctc_amount='$ctc_amount',
            ctc_percentage='$ctc_percentage',
            total_ctc='$total_ctc',
            total_amount='$total_amount',
            insert_login_id='$user_id',
            created_date=NOW()
        ");
    }
}

$result = 1;

echo json_encode([
    'result' => $result,
    'last_id' => $staff_profile_id,
]);