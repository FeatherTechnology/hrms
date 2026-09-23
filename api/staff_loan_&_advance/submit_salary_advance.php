<?php
// <!-- to submit the staff salary advance -->

require '../../ajaxconfig.php';
@session_start();

$company_name      = $_POST['company_name'];
$staff_profile_id  = $_POST['staff_profile_id'];
$advance_amount       = $_POST['advance_amount'];
$dedection_month          = $_POST['dedection_month'];
$advance_salary_id     = $_POST['advance_salary_id'];
$user_id           = $_SESSION['user_id'];

$result = 0;

if (!empty($dedection_month)) {
    $dedection_month = $dedection_month . '-01';
}

if (!empty($advance_salary_id)) {

    // Update Staff Loan
    $stmt = $pdo->prepare("
        UPDATE `staff_salary_adavance` SET
            `company_id` = ?,
            `staff_id` = ?,
            `advance_amount` = ?,
            `dedection_month` = ?,
            `update_login_id` = ?,
            `updated_date` = NOW()
        WHERE id = ?
    ");

    $qry = $stmt->execute([
        $company_name,
        $staff_profile_id,
        $advance_amount,
        $dedection_month,
        $user_id,
        $advance_salary_id
    ]);

    if ($qry) {
        $result = 1; // Update Successful
    }

} else {

    // Insert Staff Loan
    $stmt = $pdo->prepare("
        INSERT INTO `staff_salary_adavance` (
            `company_id`,
            `staff_id`,
            `advance_amount`,
            `dedection_month`,
            `insert_login_id`,
            `created_date`
        ) VALUES (
            ?, ?, ?, ?, ?, NOW()
        )
    ");

    $qry = $stmt->execute([
        $company_name,
        $staff_profile_id,
        $advance_amount,
        $dedection_month,
        $user_id
    ]);

    if ($qry) {
        $result = 2; // Insert Successful
    }
}

$pdo = null;

echo json_encode($result);
