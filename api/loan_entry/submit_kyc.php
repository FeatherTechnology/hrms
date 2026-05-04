<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['upload']['name'])) {
    $path = "../../uploads/loan_entry/kyc/";
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
    $pic = $_POST['kyc_upload'];
}
$cus_id = $_POST['cus_id']; 
$proof_of = $_POST['proof_of'];
$fam_mem = $_POST['fam_mem'];
$proof = $_POST['proof'];
$proof_detail = $_POST['proof_detail'];
$cus_profile_id= $_POST['cus_profile_id'];
//$upload= $_POST['upload'];
$user_id = $_SESSION['user_id'];
$kyc_id = $_POST['kyc_id'];

if ($kyc_id != '') {
    if ($proof_of == '1') { 
        $qry = $pdo->query("UPDATE `kyc_info` SET `cus_id`='$cus_id',`cus_profile_id`='$cus_profile_id',`proof_of`='$proof_of',`proof`='$proof',`proof_detail`='$proof_detail',`upload`='$pic',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$kyc_id'");
    } else {

        $qry = $pdo->query("UPDATE `kyc_info` SET `cus_id`='$cus_id', `cus_profile_id`='$cus_profile_id',`proof_of`='$proof_of',`fam_mem`='$fam_mem',`proof`='$proof',`proof_detail`='$proof_detail',`upload`='$pic',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$kyc_id'");
         //update
    }
    $result = 0;
} else {

    if ($proof_of == '1') {
        // If proof_of is "Customer", set fam_mem to an empty string
        // $fam_mem = 'NULL';
        $qry = $pdo->query("INSERT INTO `kyc_info`( `cus_id`,`cus_profile_id`,`proof_of`,`proof`,`proof_detail`, `upload`,`insert_login_id`,`created_on`) VALUES ('$cus_id','$cus_profile_id','$proof_of', '$proof','$proof_detail','$pic','$user_id',now())");
    } else {
        // If proof_of is not "Customer", use the value sent from the form
        $fam_mem = $_POST['fam_mem'];
        $qry = $pdo->query("INSERT INTO `kyc_info`( `cus_id`,`cus_profile_id`,`proof_of`, `fam_mem`,`proof`,`proof_detail`, `upload`,`insert_login_id`,`created_on`) VALUES ('$cus_id','$cus_profile_id','$proof_of','$fam_mem','$proof','$proof_detail','$pic','$user_id',now())");
    }

    $result = 1;
}

echo json_encode($result);
