<?php
require '../../ajaxconfig.php';
@session_start();


$staff_id = $_POST['staff_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$fam_name = $_POST['fam_name'];
$fam_relationship = $_POST['fam_relationship'];
$fam_dob = $_POST['fam_dob'];
$fam_occupation = $_POST['fam_occupation'];
$fam_mobile = $_POST['fam_mobile'];
$user_id = $_SESSION['user_id']; // Corrected session variable name
$family_id = $_POST['family_id'];

$result = 0; // Default result value
if ($family_id != '') {
    $qry = $pdo->query("UPDATE `family_info` SET `staff_id`='$staff_id', `staff_profile_id`='$staff_profile_id', `fam_name`='$fam_name', `fam_relationship`='$fam_relationship', `fam_dob`='$fam_dob', `fam_occupation`='$fam_occupation', `fam_mobile`='$fam_mobile', `update_login_id`='$user_id', updated_on = now() WHERE `id`='$family_id'");
    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `family_info`(`staff_id`, `staff_profile_id`, `fam_name`, `fam_relationship`, `fam_dob`, `fam_occupation`, `fam_mobile`, `insert_login_id`, `created_on`) VALUES ('$staff_id', '$staff_profile_id', '$fam_name', '$fam_relationship', '$fam_dob', '$fam_occupation', '$fam_mobile', '$user_id', now())");
 if ($qry) {
        $result = 2; // Insert successfull
    }
}


echo json_encode($result);
?>
