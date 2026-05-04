<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$cus_id = $_POST['cus_id'];
$customer_profile_id = $_POST['customer_profile_id'];
$owner_name = $_POST['owner_name'];
$owner_relationship = $_POST['owner_relationship'];
$vehicle_details = $_POST['vehicle_details'];
$endorsement_name = $_POST['endorsement_name'];
$key_original = $_POST['key_original'];
$rc_original = $_POST['rc_original'];
$id = $_POST['id'];


if (!empty($_FILES['endorsement_upload']['name'])) {
    $path = "../../uploads/loan_issue/endorsement_info/";
    $picture = $_FILES['endorsement_upload']['name'];
    $pic_temp = $_FILES['endorsement_upload']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = (isset($_POST['endorsement_upload_edit'])) ? $_POST['endorsement_upload_edit'] : '';
}

$status = 0;
if ($id != '') {
    $qry = $pdo->query("UPDATE `endorsement_info` SET `cus_id`='$cus_id',`cus_profile_id`='$customer_profile_id',`owner_name`='$owner_name',`relationship`='$owner_relationship',`vehicle_details`='$vehicle_details',`endorsement_name`='$endorsement_name',`key_original`='$key_original',`rc_original`='$rc_original',`upload`='$picture',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id' ");
    $status = 1; //update
} else {
    $qry = $pdo->query("INSERT INTO `endorsement_info`(`cus_id`, `cus_profile_id`, `owner_name`, `relationship`, `vehicle_details`, `endorsement_name`, `key_original`, `rc_original`, `upload`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$customer_profile_id','$owner_name','$owner_relationship','$vehicle_details','$endorsement_name','$key_original','$rc_original','$picture','$user_id',now())");
    $status = 2; //Insert
}

echo json_encode($status);
