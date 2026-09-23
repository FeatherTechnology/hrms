<?php
// <!-- to submit staff loan  -->
require '../../ajaxconfig.php';
@session_start();

$company_name      = $_POST['company_name'];
$staff_profile_id  = $_POST['staff_profile_id'];
$loan_amount       = $_POST['loan_amount'];
$int_rate          = $_POST['int_rate'];
$due_period        = $_POST['due_period'];
$total_intrest     = $_POST['total_intrest'];
$due_amount        = $_POST['due_amount'];
$due_start_date    = $_POST['due_start_date'];
$due_end_date      = $_POST['due_end_date'];
$staff_loan_id     = $_POST['staff_loan_id'];
$user_id           = $_SESSION['user_id'];

$result = 0;

if (!empty($staff_loan_id)) {

    // Update Staff Loan
    $stmt = $pdo->prepare("
        UPDATE `staff_loan` SET
            `company_id` = ?,
            `staff_id` = ?,
            `loan_amount` = ?,
            `intrest_rate` = ?,
            `due_period` = ?,
            `total_intrest` = ?,
            `due_amount` = ?,
            `due_start_date` = ?,
            `due_end_date` = ?,
            `update_login_id` = ?,
            `updated_date` = NOW()
        WHERE id = ?
    ");

    $qry = $stmt->execute([
        $company_name,
        $staff_profile_id,
        $loan_amount,
        $int_rate,
        $due_period,
        $total_intrest,
        $due_amount,
        $due_start_date,
        $due_end_date,
        $user_id,
        $staff_loan_id
    ]);

    if ($qry) {
        $result = 1; // Update Successful
    }

} else {

    // Insert Staff Loan
    $stmt = $pdo->prepare("
        INSERT INTO `staff_loan` (
            `company_id`,
            `staff_id`,
            `loan_amount`,
            `intrest_rate`,
            `due_period`,
            `total_intrest`,
            `due_amount`,
            `due_start_date`,
            `due_end_date`,
            `insert_login_id`,
            `created_date`
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $qry = $stmt->execute([
        $company_name,
        $staff_profile_id,
        $loan_amount,
        $int_rate,
        $due_period,
        $total_intrest,
        $due_amount,
        $due_start_date,
        $due_end_date,
        $user_id
    ]);

    if ($qry) {
        $result = 2; // Insert Successful
    }
}

$pdo = null;

echo json_encode($result);
