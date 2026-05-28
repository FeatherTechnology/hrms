<?php
require '../../ajaxconfig.php';
@session_start();


$staff_id = $_POST['staff_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$exp_type = $_POST['exp_type'];
$total_experience = $_POST['total_experience'];
$pre_company = $_POST['pre_company'];
$pre_designation = $_POST['pre_designation'];
$work_duration = $_POST['work_duration'];
$last_salary = $_POST['last_salary'];
$reason_for_leaving = $_POST['reason_for_leaving'];
$user_id = $_SESSION['user_id']; // Corrected session variable name
$experience_id = $_POST['experience_id'];

$result = 0; // Default result value
if ($experience_id != '') {
    $qry = $pdo->query("UPDATE `experience_info` SET `staff_id`='$staff_id', `staff_profile_id`='$staff_profile_id', `exp_type`='$exp_type', `total_experience`='$total_experience', `pre_company`='$pre_company', `pre_designation`='$pre_designation', `work_duration`='$work_duration', `last_salary`='$last_salary', `reason_for_leaving`='$reason_for_leaving', `update_login_id`='$user_id', updated_on = now() WHERE `id`='$experience_id'");
    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `experience_info`(`staff_id`, `staff_profile_id`, `exp_type`, `total_experience`, `pre_company`, `pre_designation`, `work_duration`, `last_salary`, `reason_for_leaving`, `insert_login_id`, `created_on`) VALUES ('$staff_id', '$staff_profile_id', '$exp_type', '$total_experience', '$pre_company', '$pre_designation', '$work_duration', '$last_salary', '$reason_for_leaving', '$user_id', now())");
 if ($qry) {
        $result = 2; // Insert successfull
    }
}


echo json_encode($result);
?>
