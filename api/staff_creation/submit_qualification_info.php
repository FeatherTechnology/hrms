<?php
require '../../ajaxconfig.php';
@session_start();


$staff_id = $_POST['staff_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$highest_qualification = $_POST['highest_qualification'];
$degree = $_POST['degree'];
$specialization = $_POST['specialization'];
$college = $_POST['college'];
$university = $_POST['university'];
$year_of_passing = $_POST['year_of_passing'];
$user_id = $_SESSION['user_id']; // Corrected session variable name
$qualification_id = $_POST['qualification_id'];

$result = 0; // Default result value
if ($qualification_id != '') {
    $qry = $pdo->query("UPDATE `qualification_info` SET `staff_id`='$staff_id', `staff_profile_id`='$staff_profile_id', `highest_qualification`='$highest_qualification', `degree`='$degree', `specialization`='$specialization', `college`='$college', `university`='$university', `year_of_passing`='$year_of_passing', `update_login_id`='$user_id', updated_on = now() WHERE `id`='$qualification_id'");
    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `qualification_info`(`staff_id`, `staff_profile_id`, `highest_qualification`, `degree`, `specialization`, `college`, `university`, `year_of_passing`, `insert_login_id`, `created_on`) VALUES ('$staff_id', '$staff_profile_id', '$highest_qualification', '$degree', '$specialization', '$college', '$university', '$year_of_passing', '$user_id', now())");
 if ($qry) {
        $result = 2; // Insert successfull
    }
}


echo json_encode($result);
?>
