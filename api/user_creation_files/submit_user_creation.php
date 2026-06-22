<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$user_type = $_POST['user_type'];
$director_name = $_POST['director_name'];
$multi_company_name = $_POST['multi_company_name'] ?? [];

if (is_array($multi_company_name)) {
    $multi_company_name = implode(',', $multi_company_name);
}
$company_name = $_POST['company_name'];
$role = $user_type;
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

    if ($id != '0' && $id != '') {
        $qry = $pdo->query("UPDATE `users` SET `user_type`='$user_type',`director_name` = '$director_name ',`director_company` = '$multi_company_name', `company_id`='$company_name',`role`='$role',`staff_name_id`='$staff_name', `staff_id`='$staff_id',`user_name`='$user_name',`password`='$password',`confirm_password`='$confirm_password',`download_access`='$download_access', `report_access`= '$report_access', `home_access`='$home_access',`screens`='$submenus', `status` = '0', `update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
        if ($qry) {
            $status = '1';
            $last_id = $id;
        }
    } else {
        $qry = $pdo->query("INSERT INTO `users`(`user_type`,`director_name`,`director_company`, `company_id`, `role`, `staff_name_id`, `staff_id`, `user_name`, `password`, `confirm_password`, `download_access`, `report_access`,`home_access`, `screens`, `insert_login_id`, `created_on`) VALUES ('$user_type','$director_name','$multi_company_name', '$company_name', '$role','$staff_name', '$staff_id', '$user_name', '$password', '$confirm_password', '$download_access','$report_access','$home_access', '$submenus','$user_id',now())");
        if ($qry) {
            $status = '2';
            $last_id = $pdo->lastInsertId();
        }
    }
    // Commit transaction
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
$result = array('status' => $status, 'last_id' => $last_id);
echo json_encode($result);
