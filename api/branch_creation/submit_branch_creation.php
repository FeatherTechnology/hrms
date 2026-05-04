<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$branch_code=$_POST['branch_code'];
$branch_name=$_POST['branch_name'];
$address = $_POST['address'];
$state = $_POST['state'];
$district = $_POST['district'];
$taluk = $_POST['taluk'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$email_id = $_POST['email_id'];
$mobile_number = $_POST['mobile_number'];
$whatsapp = $_POST['whatsapp'];
$landline = $_POST['landline'];
$landline_code = $_POST['landline_code'];
$user_id = $_SESSION['user_id'];
$branchid = $_POST['branchid'];

$result = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest Branch code
    $selectIC = $pdo->query("SELECT branch_code FROM branch_creation WHERE branch_code != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");
    if ($branchid !='0' && $branchid !='') {
        $qry = $pdo->query("UPDATE `branch_creation` SET `company_name`='$company_name',`branch_code`='$branch_code',`branch_name`='$branch_name',`address`='$address',`state`='$state',`district`='$district',`taluk`='$taluk',`place`='$place',`pincode`='$pincode',`email_id`='$email_id',`mobile_number`='$mobile_number',`whatsapp`='$whatsapp',`landline_code`='$landline_code',`landline`='$landline',`update_login_id`='$user_id',updated_date = now() WHERE `id`='$branchid'");
        if ($qry) {
            $result = 1; //update
        }
    } else {
        $str = preg_replace('/\s+/', '', $company_name);
        $myStr = mb_substr($str, 0, 1);
        if ($selectIC->rowCount() > 0) {
            $row = $selectIC->fetch();
            $ac2 = $row["branch_code"];
            $appno2 = ltrim(strstr($ac2, '-'), '-');
            $appno2 = $appno2 + 1;
            $branch_code = $myStr . "-" . $appno2;
        } else {
            $initialapp = $myStr . "-101";
            $branch_code = $initialapp;
        }
        $qry = $pdo->query("INSERT INTO `branch_creation`(`company_name`, `branch_code`,`branch_name`,`address`, `state`, `district`, `taluk`, `place`, `pincode`,`email_id`, `mobile_number`, `whatsapp`, `landline_code`,`landline`, `insert_login_id`,`created_date`) VALUES ('$company_name','$branch_code', '$branch_name','$address','$state','$district','$taluk','$place','$pincode','$email_id','$mobile_number','$whatsapp','$landline_code','$landline','$user_id',now())");

        if ($qry) {
            $result = 2; //Insert
        }
    } // Commit transaction
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
$pdo = null;
echo json_encode($result);
