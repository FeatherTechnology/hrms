<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['upload']['name'])) {
    $path = "../../uploads/staff_creation/document/";
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
$staff_id = $_POST['staff_id'];
$staff_profile_id = $_POST['staff_profile_id'];
$doc_name = $_POST['doc_name'];
$doc_type = $_POST['doc_type'];
$user_id = $_SESSION['user_id'];
$document_id = $_POST['document_id'];
$result = '0';
if ($document_id != '') {
    $qry = $pdo->query("UPDATE `document_info` SET `staff_id`='$staff_id',  `staff_profile_id`='$staff_profile_id',`doc_name`='$doc_name',`doc_type`='$doc_type',`upload`='$pic',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$document_id'");
    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `document_info`( `staff_id`,`staff_profile_id`,`doc_name`,`doc_type`, `upload`,`insert_login_id`,`created_on`) VALUES ('$staff_id','$staff_profile_id','$doc_name','$doc_type','$pic','$user_id',now())");
    if ($qry) {
        $result = 2; // Insert successfull
    }
}


echo json_encode($result);
