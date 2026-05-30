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
$department_name = $_POST['department_name'];
$department_name2 = isset($_POST['department_name2']) ? explode(',', $_POST['department_name2']) : [];
$designation_name = $_POST['designation_name'];
$designation_name2 = isset($_POST['designation_name2']) ? explode(',', $_POST['designation_name2']) : [];
$website = $_POST['website'];
$mailid = $_POST['mailid'];
$instagram = $_POST['instagram'];
$youtube_link = $_POST['youtube_link'];
$facebook = $_POST['facebook'];
$twitter = $_POST['twitter'];

$user_id = $_SESSION['user_id'];
$companyid = $_POST['companyid'];


if ($companyid != '') {

    $qry = $pdo->query("UPDATE `company_creation` SET `company_name`='$company_name',`gst_num`='$gst_number', `cin_number`='$cin_number', `address`='$address',`state`='$state',`district`='$district',`place`='$place',`pincode`='$pincode',`mobile`='$mobile',`whatsapp`='$whatsapp',`landline_code`='$landline_code',`landline`='$landline',`website`='$website',`mailid`='$mailid',`instagram`='$instagram',`youtube_link`='$youtube_link',`facebook`='$facebook',`twitter`='$twitter',`status`='1',`update_user_id`='$user_id',`updated_date`=now() WHERE `id`='$companyid'");

    // Calculate deleted and newly added IDs
    $department_name = array_map('intval', $department_name);
    $designation_name = array_map('intval', $designation_name);

    $department_to_delete = array_diff($department_name2, $department_name);
    $department_to_insert = array_diff($department_name, $department_name2);

    $designation_to_delete = array_diff($designation_name2, $designation_name);
    $designation_to_insert = array_diff($designation_name, $designation_name2);

    // Delete unselected departments
    foreach ($department_to_delete as $department_del_id) {
        $pdo->query("DELETE FROM company_department_mapping WHERE company_id = $companyid AND department_id = $department_del_id");
    }

    // Insert new department_ids
    foreach ($department_to_insert as $department_new_id) {
        $pdo->query("INSERT INTO company_department_mapping (company_id, department_id) VALUES ($companyid, $department_new_id)");
    }

    // // Delete unselected designations
    foreach ($designation_to_delete as $designation_del_id) {
        $pdo->query("DELETE FROM company_designation_mapping WHERE company_id = $companyid AND designation_id = $designation_del_id");
    }

    // // Insert new designation_ids   
    foreach ($designation_to_insert as $designation_new_id) {
        $pdo->query("INSERT INTO company_designation_mapping (company_id, designation_id) VALUES ($companyid, $designation_new_id)");
    }

    if ($pdo) {
        $result = 0; //update successful
    }
} else {

    $qry = $pdo->query("INSERT INTO `company_creation`(`company_name`,`gst_num`,`cin_number`, `address`, `state`, `district`, `place`, `pincode`,`mobile`, `whatsapp`, `landline_code`, `landline`, `website`, `mailid`, `instagram`,`youtube_link`,`facebook`,`twitter`, `insert_user_id`, `created_date`) VALUES ('$company_name','$gst_number','$cin_number','$address','$state','$district','$place','$pincode','$mobile','$whatsapp','$landline_code', '$landline','$website','$mailid','$instagram','$youtube_link','$facebook','$twitter','$user_id', now())");

    $company_id = $pdo->lastInsertId();

    // Insert into department mapping table
    foreach ($department_name as $department_id) {
        $department_id = (int)trim($department_id);
        if ($department_id > 0) {
            $departmentQry = "INSERT INTO company_department_mapping (company_id, department_id) VALUES ($company_id, $department_id)";
            $pdo->query($departmentQry) or die("Error inserting department map: " . $pdo->errorInfo());
        }
    }

    // Insert into designation mapping table
    foreach ($designation_name as $designation_id) {
        $designation_id = (int)trim($designation_id);
        if ($designation_id > 0) {
            $designationQry = "INSERT INTO company_designation_mapping (company_id, designation_id) VALUES ($company_id, $designation_id)";
            $pdo->query($designationQry) or die("Error inserting designation map: " . $pdo->errorInfo());
        }
    }

    if ($pdo) {
        $result = 1; //Insert successfull
    }
}

echo json_encode($result);
