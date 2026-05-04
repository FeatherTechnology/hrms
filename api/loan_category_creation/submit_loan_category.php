<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$loan_category = $_POST['loanCategoryName'];
$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM `loan_category` WHERE REPLACE(TRIM(loan_category), ' ', '') = REPLACE(TRIM('$loan_category'), ' ', '') ");
if ($qry->rowCount() > 0) {
    $result = 2; //already Exists.

} else {
    if ($id != '0' && $id != '') {
        $pdo->query("UPDATE `loan_category` SET `loan_category`='$loan_category',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id' ");
        $result = 0; //update

    } else {
        $pdo->query("INSERT INTO `loan_category`(`loan_category`, `insert_login_id`, `created_on`) VALUES ('$loan_category','$user_id', now())");
        $result = 1; //Insert
    }
}

echo json_encode($result);
