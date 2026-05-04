<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$property_holder_name = $_POST['property_holder_name'];
$mort_relationship = $_POST['mort_relationship'];
$mort_property_details = $_POST['mort_property_details'];
$mortgage_name = $_POST['mortgage_name'];
$mort_designation = $_POST['mort_designation'];
$mortgage_no = $_POST['mortgage_no'];
$reg_office = $_POST['reg_office'];
$mortgage_value = $_POST['mortgage_value'];
$cus_id = $_POST['cus_id'];
$customer_profile_id = $_POST['customer_profile_id'];
$id = $_POST['id'];


if (!empty($_FILES['mort_upload']['name'])) {
    $path = "../../uploads/loan_issue/mortgage_info/";
    $picture = $_FILES['mort_upload']['name'];
    $pic_temp = $_FILES['mort_upload']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = (isset($_POST['mort_upload_edit'])) ? $_POST['mort_upload_edit'] : ''; 
}

$status = 0;
if ($id != '') {
    $qry = $pdo->query("UPDATE `mortgage_info` SET `cus_id`='$cus_id',`cus_profile_id`='$customer_profile_id',`property_holder_name`='$property_holder_name',`relationship`='$mort_relationship',`property_details`='$mort_property_details',`mortgage_name`='$mortgage_name',`designation`='$mort_designation',`mortgage_number`='$mortgage_no',`reg_office`='$reg_office',`mortgage_value`='$mortgage_value',`upload`='$picture',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id' ");
    $status = 1; //update
} else {
    $qry = $pdo->query("INSERT INTO `mortgage_info`(`cus_id`, `cus_profile_id`, `property_holder_name`, `relationship`, `property_details`, `mortgage_name`, `designation`, `mortgage_number`, `reg_office`, `mortgage_value`, `upload`, `insert_login_id`,  `created_on`) VALUES ('$cus_id','$customer_profile_id','$property_holder_name','$mort_relationship','$mort_property_details','$mortgage_name','$mort_designation','$mortgage_no','$reg_office','$mortgage_value','$picture','$user_id',now())");
    $status = 2; //Insert
}

echo json_encode($status);
