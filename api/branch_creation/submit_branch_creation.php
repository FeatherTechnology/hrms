<?php
require '../../ajaxconfig.php';
@session_start();

$company_id = $_POST['company_name'];
$branch_code = $_POST['branch_code'];
$branch_name = $_POST['branch_name'];
$address = $_POST['address'];
$state = $_POST['state'];
$district = $_POST['district'];
$place = $_POST['place'];
$pincode = $_POST['pincode'];
$location = $_POST['location'];
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
    if ($branchid != '0' && $branchid != '') {
        $qry = $pdo->query("UPDATE `branch_creation` SET `company_id`='$company_id',`branch_code`='$branch_code',`branch_name`='$branch_name',`address`='$address',`state`='$state',`district`='$district',`place`='$place',`pincode`='$pincode', `location`='$location',`email_id`='$email_id',`mobile_number`='$mobile_number',`whatsapp`='$whatsapp',`landline_code`='$landline_code',`landline`='$landline',`update_login_id`='$user_id',updated_date = now() WHERE `id`='$branchid'");
        if ($qry) {
            $result = 1; //update
        }
    } else {

        $qry1 = $pdo->query("SELECT company_name FROM company_creation WHERE id = '$company_id'");
        $qry_info = $qry1->fetch();
        $company_name = trim($qry_info["company_name"]);
        // Split words
        $words = preg_split('/\s+/', $company_name);
        // Generate prefix
        $prefix = '';
        if (count($words) > 1) {
            // Multiple words
            foreach ($words as $word) {
                $prefix .= strtoupper(mb_substr($word, 0, 1));
            }
        } else {
            // Single word
            $prefix = strtoupper(mb_substr($company_name, 0, 1));
        }

        // Get last branch code
        $qry = $pdo->query("SELECT MAX(branch_code) as branch_code FROM branch_creation WHERE company_id = '$company_id'");
        $row = $qry->fetch(PDO::FETCH_ASSOC);
        if ($row["branch_code"] != '') {
            // Example: MC-101
            $ac2 = $row["branch_code"];
            $appno2 = ltrim(strstr($ac2, '-'), '-');
            $appno2 = (int)$appno2 + 1;
            $branch_code = $prefix . "-" . $appno2;
        } else {
            // Initial code
            $branch_code = $prefix . "-101";
        }

        $qry = $pdo->query("INSERT INTO `branch_creation`(`company_id`, `branch_code`,`branch_name`,`address`, `state`, `district`, `place`, `pincode`,`location`, `email_id`, `mobile_number`, `whatsapp`, `landline_code`,`landline`, `insert_login_id`,`created_date`) VALUES ('$company_id','$branch_code', '$branch_name','$address','$state','$district','$place','$pincode','$location','$email_id','$mobile_number','$whatsapp','$landline_code','$landline','$user_id',now())");

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
