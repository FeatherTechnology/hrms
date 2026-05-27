<?php
require '../../ajaxconfig.php';
@session_start();

$staff_id = $_POST['staff_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$branch_name_three = $_POST['branch_name_three'];
$branch_location = $_POST['branch_location'];
$reason = $_POST['reason'];
$location_access_id = $_POST['location_access_id'];
$user_id = $_SESSION['user_id'];

$result = 0;

$checkQry = $pdo->query("SELECT id FROM location_access_mapping WHERE staff_profile_id = '$staff_profile_id' AND status = 0

    AND (
        '$from_date' BETWEEN from_date AND to_date
        OR
        '$to_date' BETWEEN from_date AND to_date
        OR
        from_date BETWEEN '$from_date' AND '$to_date'
        OR
        to_date BETWEEN '$from_date' AND '$to_date'
    )

    AND id != '$location_access_id'
");

if ($checkQry->rowCount() > 0) {
    $result = 3; // Already exists
} else {
    if ($location_access_id != '') {
        $qry = $pdo->query("UPDATE `location_access_mapping` SET `staff_id`='$staff_id', `staff_profile_id`='$staff_profile_id', `from_date`='$from_date', `to_date`='$to_date', `assigned_branch`='$branch_name_three', `lattitude_longitude`='$branch_location', `reason`='$reason', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$location_access_id'");

        if ($qry) {
            $result = 1; // Update successfull
        }
    } else {
        $qry = $pdo->query("INSERT INTO `location_access_mapping` (`staff_id`, `staff_profile_id`, `from_date`, `to_date`, `assigned_branch`, `lattitude_longitude` , `reason`, `insert_login_id`) VALUES ('$staff_id', '$staff_profile_id', '$from_date', '$to_date', '$branch_name_three', '$branch_location', '$reason', '$user_id')");

        if ($qry) {
            $result = 2; // Insert successfull
        }
    }
}



$pdo = null; // Close Connection

echo json_encode($result);
