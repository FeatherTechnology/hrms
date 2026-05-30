<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$user_code = $_POST['user_code'];
$company_name = $_POST['company_name'];
$role = $_POST['role'];
$staff_name = $_POST['staff_name'];
$staff_id = $_POST['staff_id'];
$user_name = $_POST['user_name'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$download_access = $_POST['download_access'];
$report_access = $_POST['report_access'];
$home_access = $_POST['home_access'];
$submenus = $_POST['submenus'];

if (!empty($submenus)) {
    array_unshift($submenus, '1');
}

$submenus = implode(',', $submenus);

$id = $_POST['id'];
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest Branch code
    $selectIC = $pdo->query("SELECT user_code FROM users WHERE user_code != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");
    $qry = $pdo->query("SELECT * FROM users WHERE REPLACE(TRIM(user_name), ' ', '') = REPLACE(TRIM('$user_name'), ' ', '') AND `user_code` !='$user_code' ");
    if ($qry->rowCount() > 0) {
        $status = '3';
        $last_id = '0'; //Already exists.
    } else {
        if ($id != '0' && $id != '') {
            $qry = $pdo->query("UPDATE `users` SET `user_code`='$user_code',`company_id`='$company_name',`role`='$role',`staff_name_id`='$staff_name', `staff_id`='$staff_id',`user_name`='$user_name',`password`='$password',`confirm_password`='$confirm_password',`download_access`='$download_access', `report_access`= '$report_access', `home_access`='$home_access',`screens`='$submenus', `status` = '0', `update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
            if ($qry) {
                $status = '1';
                $last_id = $id;
            }
        } else {
            if ($selectIC->rowCount() > 0) {
                $row = $selectIC->fetch();
                $usr_code_f = substr($row['user_code'], 0, 3);
                $usr_code_s = substr($row['user_code'], 3, 5);
                $final_code = str_pad($usr_code_s + 1, 3, 0, STR_PAD_LEFT);
                $user_code_final = $usr_code_f . $final_code;
            } else {
                $user_code_final = "US-" . "001";
            }
            $qry = $pdo->query("INSERT INTO `users`(`user_code`, `company_id`, `role`, `staff_name_id`, `staff_id`, `user_name`, `password`, `confirm_password`, `download_access`, `report_access`,`home_access`, `screens`, `insert_login_id`, `created_on`) VALUES ('$user_code', '$company_name', '$role','$staff_name', '$staff_id', '$user_name', '$password', '$confirm_password', '$download_access','$report_access','$home_access', '$submenus','$user_id',now())");
            if ($qry) {
                $status = '2';
                $last_id = $pdo->lastInsertId();
            }
        }
    } // Commit transaction
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
$result = array('status' => $status, 'last_id' => $last_id);
echo json_encode($result);
