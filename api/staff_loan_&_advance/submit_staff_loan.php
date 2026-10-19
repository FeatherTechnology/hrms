<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'] ?? '';
$branch_name = $_POST['branch_name'] ?? '';
$staff_profile_id = $_POST['staff_profile_id'] ?? '';
$loan_amount = $_POST['loan_amount'] ?? 0;
$int_rate = $_POST['int_rate'] ?? 0;
$due_period = $_POST['due_period'] ?? 0;
$total_intrest = $_POST['total_intrest'] ?? 0;
$due_amount = $_POST['due_amount'] ?? 0;
$due_start_date = $_POST['due_start_date'] ?? '';
$due_end_date = $_POST['due_end_date'] ?? '';
$staff_loan_id = $_POST['staff_loan_id'] ?? '';
$type = $_POST['type'] ?? '';
$deduction_month = $_POST['deduction_month'] ?? '';
$deduction_amount = $_POST['deduction_amount'] ?? '';
$bal_amount = $_POST['bal_amount'] ?? 0;
$user_id = $_SESSION['user_id'] ?? '';

$result = 0;

try {
    if (!empty($staff_loan_id)) {

        $deduction_month .= '-01';

        $check = $pdo->prepare("
            SELECT id
            FROM staff_deduction
            WHERE staff_loan_id = ?
              AND deduction_month = ?
            LIMIT 1
        ");
        $check->execute([$staff_loan_id, $deduction_month]);

        if ($check->fetch()) {
            echo json_encode(3);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO staff_deduction (
                staff_loan_id,
                staff_id,
                deduction_amount,
                deduction_month,
                bal_amout,
                insert_login_id,
                created_date
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if ($stmt->execute([
            $staff_loan_id,
            $staff_profile_id,
            $deduction_amount,
            $deduction_month,
            $bal_amount,
            $user_id
        ])) {
            $result = 1;
        }

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO staff_loan (
                company_id,
                branch_id,
                staff_id,
                type,
                loan_amount,
                intrest_rate,
                due_period,
                total_intrest,
                due_amount,
                due_start_date,
                due_end_date,
                insert_login_id,
                created_date
            ) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        if ($stmt->execute([
            $company_name,
            $branch_name,
            $staff_profile_id,
            $type,
            $loan_amount,
            $int_rate,
            $due_period,
            $total_intrest,
            $due_amount,
            $due_start_date,
            $due_end_date ?: null,
            $user_id
        ])) {
            $result = 2;
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $result = 0;
}

$pdo = null;

echo json_encode($result);
?>
