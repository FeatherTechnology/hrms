<?php
require '../../ajaxconfig.php';
@session_start();
$cus_id = $_POST['cus_id']; 
$bank_name = $_POST['bank_name'];
$branch_name = $_POST['branch_name'];
$acc_holder_name = $_POST['acc_holder_name'];
$acc_number=$_POST['acc_number'];
$ifsc_code=$_POST['ifsc_code'];
$user_id = $_SESSION['user_id'];
$bank_id = $_POST['bank_id'];
$cus_profile_id = $_POST['cus_profile_id'];

if($bank_id !=''){
    
    $qry = $pdo->query("UPDATE `bank_info` SET `cus_id`='$cus_id',`cus_profile_id`='$cus_profile_id',`bank_name`='$bank_name',`branch_name`='$branch_name',`acc_holder_name`='$acc_holder_name',`acc_number`='$acc_number',`ifsc_code`='$ifsc_code',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$bank_id'");
    $result = 0; //update

}else{
    $qry = $pdo->query("INSERT INTO `bank_info`(`cus_id`,`cus_profile_id`,`bank_name`, `branch_name`,`acc_holder_name`,`acc_number`, `ifsc_code`,`insert_login_id`,`created_on`) VALUES ('$cus_id','$cus_profile_id','$bank_name','$branch_name', '$acc_holder_name','$acc_number','$ifsc_code','$user_id',now())");
    $result = 1; //Insert
}

echo json_encode($result);
?>