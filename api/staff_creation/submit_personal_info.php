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
$company_id = $_POST['company_name'];
$staff_name = $_POST['staff_name'];
$staff_type = $_POST['staff_type'];
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
$user_id = $_SESSION['user_id'];
$staff_profile_id = $_POST['staff_profile_id'];


$result = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();

   
    // Get Company Name
    $qry = $pdo->query("SELECT company_name FROM company_creation WHERE id = '$company_id'");
    $row = $qry->fetch(PDO::FETCH_ASSOC);

    $company_name = trim($row['company_name']);

    // Remove extra spaces
    $company_name = preg_replace('/\s+/', ' ', $company_name);

    // Split words
    $words = explode(' ', $company_name);

    // Take first letter of all words
    $prefix = '';

    foreach ($words as $word) {
        if ($word != '') {
            $prefix .= strtoupper(substr($word, 0, 1));
        }
    }

    // Add S for Staff
    $prefix .= 'S';
    // Get Last Staff ID
    $qry = $pdo->query("SELECT MAX(staff_id) as staff_id 
                        FROM staff_creation 
                        WHERE company_id = '$company_id'");

    $row = $qry->fetch(PDO::FETCH_ASSOC);

    if ($row['staff_id'] != '') {

        $last_id = $row['staff_id'];
        $number = explode('-', $last_id)[1];

        $new_number = (int)$number + 1;

        $staff_id = $prefix . '-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

    } else {

        $staff_id = $prefix . '-001';
    }
    $qry = $pdo->query("INSERT INTO `staff_creation`(`company_id`, `staff_id`, `staff_name`, `staff_type`, `address`, `gender`, `state`, `district`, `place`, `pincode`, `dob`, `age`, `blood_group`, `marital_status`, `spouse_name`, `anniversary_date`, `joining_date`, `relieve_date`, `notice_period`,`email`, `mobile1`, `mobile2`, `whatsapp`, `instagram`, `facebook`, `acc_holder_name`, `acc_number`, `bank_name`, `ifsc_code`, `bank_branch`, `status`, `insert_login_id`, `created_on` ) VALUES ('$company_id','$staff_id','$staff_name','$staff_type','$address','$gender','$state','$district','$place','$pincode','$dob', '$age','$blood_group','$marital_status','$spouse_name','$anniversary_date','$joining_date','$relieve_date','$notice_period','$email','$mobile1','$mobile2','$whatsapp','$instagram','$facebook','$acc_holder_name','$acc_number','$bank_name','$ifsc_code','$bank_branch','0','$user_id',CURRENT_TIMESTAMP())");
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
