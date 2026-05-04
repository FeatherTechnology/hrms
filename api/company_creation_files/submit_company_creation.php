<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$gst_number = $_POST['gst_number'];
$cin_number = $_POST['cin_number'];
$address = $_POST['address'];
$state = $_POST['state'];
$district = $_POST['district'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$mobile = $_POST['mobile'];
$whatsapp = $_POST['whatsapp'];
$landline_code = $_POST['landline_code'];
$landline = $_POST['landline'];
$website = $_POST['website'];
$mailid = $_POST['mailid'];
$instagram = $_POST['instagram'];
$youtube_link = $_POST['youtube_link'];
$facebook = $_POST['facebook'];
$twitter = $_POST['twitter'];

$user_id = $_SESSION['user_id'];
$companyid = $_POST['companyid'];


if($companyid !=''){

    $qry = $pdo->query("UPDATE `company_creation` SET `company_name`='$company_name',`gst_num`='$gst_number', `cin_number`='$cin_number', `address`='$address',`state`='$state',`district`='$district',`place`='$place',`pincode`='$pincode',`mobile`='$mobile',`whatsapp`='$whatsapp',`landline_code`='$landline_code',`landline`='$landline',`website`='$website',`mailid`='$mailid',`instagram`='$instagram',`youtube_link`='$youtube_link',`facebook`='$facebook',`twitter`='$twitter',`status`='1',`update_user_id`='$user_id',`updated_date`=now() WHERE `id`='$companyid'");
    $result = 0; //update

}else{
    
    $qry = $pdo->query("INSERT INTO `company_creation`(`company_name`,`gst_num`,`cin_number`, `address`, `state`, `district`, `place`, `pincode`,`mobile`, `whatsapp`, `landline_code`, `landline`, `website`, `mailid`, `instagram`,`youtube_link`,`facebook`,`twitter`, `insert_user_id`, `created_date`) VALUES ('$company_name','$gst_number','$cin_number','$address','$state','$district','$place','$pincode','$mobile','$whatsapp','$landline_code', '$landline','$website','$mailid','$instagram','$youtube_link','$facebook','$twitter','$user_id', now())");
    $result = 1; //Insert
}

echo json_encode($result);
?>