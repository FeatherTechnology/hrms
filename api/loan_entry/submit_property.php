<?php
require '../../ajaxconfig.php';
@session_start();
$cus_id = $_POST['cus_id']; 
$cus_profile_id= $_POST['cus_profile_id'];
$property = $_POST['property'];
$property_detail=$_POST['property_detail'];
$property_holder=$_POST['property_holder'];
$user_id = $_SESSION['user_id'];
$property_id = $_POST['property_id'];

if($property_id !=''){
    $qry = $pdo->query("UPDATE `property_info` SET `cus_id`='$cus_id',`cus_profile_id`='$cus_profile_id',`property`='$property',`property_detail`='$property_detail',`property_holder`='$property_holder',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$property_id'");
    $result = 0; //update

}else{
    $qry = $pdo->query("INSERT INTO `property_info`(`cus_id`,`cus_profile_id`,`property`, `property_detail`,`property_holder`,`insert_login_id`,`created_on`) VALUES ('$cus_id','$cus_profile_id','$property','$property_detail', '$property_holder','$user_id',now())");
    $result = 1; //Insert
}

echo json_encode($result);
?>