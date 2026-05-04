<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$cq_holder_type = $_POST['cq_holder_type'];
$cq_holder_name = $_POST['cq_holder_name'];
$cq_holder_id = $_POST['cq_holder_id'];
$cq_relationship = $_POST['cq_relationship'];
$cq_bank_name = $_POST['cq_bank_name'];
$cheque_count = $_POST['cheque_count'];
$cheque_upd_no  = explode(',', $_POST['cheque_no']); //stored each numbers in an array
$customer_profile_id = $_POST['customer_profile_id'];
$cq_upload_edit = $_POST['cq_upload_edit'];
$cus_id = $_POST['cus_id'];
$id = $_POST['id'];

$status = 0;
if ($id != '') {
    $pdo->query("DELETE FROM `cheque_upd` WHERE cheque_info_id = '$id'");
    $pdo->query("DELETE FROM `cheque_no_list` WHERE cheque_info_id = '$id'");
    if (isset($_POST['cq_upload_edit']) && !empty($_POST['cq_upload_edit'])) {
        $existingUploads = explode(',', $_POST['cq_upload_edit']);
        foreach ($existingUploads as $file) {
            $file = trim($file);
            if ($file !== '') {
                $pdo->query("INSERT INTO `cheque_upd`(`cus_id`, `cus_profile_id`, `cheque_info_id`, `uploads`) VALUES ('$cus_id','$customer_profile_id','$id','$file')");
            }
        }
    }
    $qry = $pdo->query("UPDATE `cheque_info` SET `cus_id`='$cus_id',`cus_profile_id`='$customer_profile_id',`holder_type`='$cq_holder_type',`holder_name`='$cq_holder_name',`holder_id`='$cq_holder_id',`relationship`='$cq_relationship',`bank_name`='$cq_bank_name',`cheque_cnt`='$cheque_count',`update_login_id`='$user_id',`updated_on`=now() WHERE id = '$id' ");
    $status = 1; //update
    $last_id = $id;
} else {
    $qry = $pdo->query("INSERT INTO `cheque_info`( `cus_id`, `cus_profile_id`, `holder_type`, `holder_name`, `holder_id`, `relationship`, `bank_name`, `cheque_cnt`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$customer_profile_id','$cq_holder_type','$cq_holder_name','$cq_holder_id','$cq_relationship','$cq_bank_name','$cheque_count','$user_id',now() )");
    $status = 2; //Insert
    $last_id = $pdo->lastInsertId();
}

if (!empty($_FILES['cq_upload']['name'])) {
    $filesArray = $_FILES['cq_upload']; //files passed as array
    
    foreach ($filesArray['name'] as $key => $val) {
        $fileName = basename($filesArray['name'][$key]);
        $targetFilePath = "../../uploads/loan_issue/cheque_info/" . $fileName;

        $fileExtension = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $uniqueFileName = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/loan_issue/cheque_info/" . $uniqueFileName)) {
            $uniqueFileName = uniqid() . '.' . $fileExtension;
        }

        // Upload file to server  
        if (move_uploaded_file($filesArray["tmp_name"][$key], "../../uploads/loan_issue/cheque_info/" . $uniqueFileName)) {
            $update =  $pdo->query("INSERT INTO `cheque_upd`(`cus_id`, `cus_profile_id`, `cheque_info_id`, `uploads`) VALUES ('$cus_id','$customer_profile_id','$last_id','$uniqueFileName')");
        }
    }
}

if ($cheque_upd_no != '') {
    
    foreach ($cheque_upd_no as $chequeNo) {
        $insert  = $pdo->query("INSERT INTO `cheque_no_list`(`cus_id`, `cus_profile_id`, `cheque_info_id`, `cheque_no`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$customer_profile_id','$last_id','$chequeNo','$user_id',now())");
    }
}

echo json_encode($status);
