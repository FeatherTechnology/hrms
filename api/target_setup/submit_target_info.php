<?php
session_start();
require "../../ajaxconfig.php";
try {
    $user_id = $_SESSION['user_id'] ?? 0;
    $target_id = (int)($_POST['target_id'] ?? 0);
    $company_id = (int)($_POST['company_id'] ?? 0);
    $effective_month = trim($_POST['effective_month'] ?? '');

    $branch_id = $_POST['branch_id'] ?? [];
    $department_id = $_POST['department_id'] ?? [];

    $staff_id = $_POST['staff_id'] ?? [];
    $incentive_title = $_POST['incentive_title'] ?? [];
    $target_count = $_POST['target_count'] ?? [];
    $target_amount = $_POST['target_amount'] ?? [];
    $minimum_count = $_POST['minimum_count'] ?? [];
    $minimum_amount = $_POST['minimum_amount'] ?? [];
    $payout_type = $_POST['payout_type'] ?? [];
    $ctc_percentage = $_POST['ctc_percentage'] ?? [];
    $fixed_amount = $_POST['fixed_amount'] ?? [];

    /* ================= VALIDATION ================= */

    $month = DateTime::createFromFormat('Y-m', $effective_month);

    if (!$month || $month->format('Y-m') !== $effective_month) {
        throw new Exception("Invalid effective month.");
    }

    /* ================= CONVERT MAPPING IDS ================= */

    if (!is_array($branch_id)) {
        $branch_id = explode(',', $branch_id);
    }

    if (!is_array($department_id)) {
        $department_id = explode(',', $department_id);
    }

    $branch_id = array_values(array_unique(array_filter(array_map('intval', $branch_id))));
    $department_id = array_values(array_unique(array_filter(array_map('intval', $department_id))));

    if (empty($branch_id) && empty($department_id)) {
        throw new Exception("Please select at least one branch or department.");
    }

    /* ================= STAFF VALIDATION ================= */

    if (empty($staff_id) || !is_array($staff_id)) {
        throw new Exception("Please add at least one staff member.");
    }

    $valid_staff_count = 0;

    foreach ($staff_id as $key => $staff) {


        $target_count_value = trim((string)($target_count[$key] ?? ''));
        $target_amount_value = trim((string)($target_amount[$key] ?? ''));

        $minimum_count_value = trim((string)($minimum_count[$key] ?? ''));
        $minimum_amount_value = trim((string)($minimum_amount[$key] ?? ''));
        $payout = trim($payout_type[$key] ?? '');
        if ($payout === 'CTC') {
            $percentage = trim((string)($ctc_percentage[$key] ?? ''));

            if ($percentage === '' || !is_numeric($percentage) || $percentage <= 0 || $percentage > 100) {
                throw new Exception("Please enter a valid CTC percentage between 0 and 100 in row " . ($key + 1) . ".");
            }
        }

        if ($payout === 'Fixed') {
            $fixed = trim((string)($fixed_amount[$key] ?? ''));

            if ($fixed === '' || !is_numeric($fixed) || $fixed <= 0) {
                throw new Exception("Please enter a valid fixed amount in row " . ($key + 1) . ".");
            }
        }

        $valid_staff_count++;
    }

    if ($valid_staff_count === 0) {
        throw new Exception("Please add at least one valid staff member.");
    }

    /* ================= TRANSACTION START ================= */

    $pdo->beginTransaction();

    /* ================= MASTER INSERT / UPDATE ================= */

    if ($target_id > 0) {

        $stmt = $pdo->prepare("
            SELECT id
            FROM target_setup
            WHERE id = ?
        ");
        $stmt->execute([$target_id]);

        if (!$stmt->fetchColumn()) {
            throw new Exception("Target setup record not found.");
        }

        $stmt = $pdo->prepare("
            UPDATE target_setup SET
                company_id = ?,
                effective_month = ?,
                update_login_id = ?,
                updated_on = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $company_id,
            $effective_month,
            $user_id,
            $target_id
        ]);

        $target_setup_id = $target_id;

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO target_setup (
                company_id,
                effective_month,
                insert_login_id,
                created_on
            ) VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([
            $company_id,
            $effective_month,
            $user_id
        ]);

        $target_setup_id = $pdo->lastInsertId();
    }

    /* ================= BRANCH MAPPING UPDATE ================= */

    $stmt = $pdo->prepare("
        SELECT branch_id
        FROM target_branch_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$target_setup_id]);

    $old_branches = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    $branch_to_delete = array_diff($old_branches, $branch_id);
    $branch_to_insert = array_diff($branch_id, $old_branches);

    $stmt_delete_branch = $pdo->prepare("
        DELETE FROM target_branch_mapping
        WHERE target_setup_id = ? AND branch_id = ?
    ");

    foreach ($branch_to_delete as $branch_del_id) {
        $stmt_delete_branch->execute([
            $target_setup_id,
            $branch_del_id
        ]);
    }

    $stmt_insert_branch = $pdo->prepare("
        INSERT INTO target_branch_mapping (
            target_setup_id,
            branch_id
        ) VALUES (?, ?)
    ");

    foreach ($branch_to_insert as $branch_new_id) {
        $stmt_insert_branch->execute([
            $target_setup_id,
            $branch_new_id
        ]);
    }

    /* ================= DEPARTMENT MAPPING UPDATE ================= */

    $stmt = $pdo->prepare("
        SELECT department_id
        FROM target_department_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$target_setup_id]);

    $old_departments = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    $department_to_delete = array_diff($old_departments, $department_id);
    $department_to_insert = array_diff($department_id, $old_departments);

    $stmt_delete_department = $pdo->prepare("
        DELETE FROM target_department_mapping
        WHERE target_setup_id = ? AND department_id = ?
    ");

    foreach ($department_to_delete as $department_del_id) {
        $stmt_delete_department->execute([
            $target_setup_id,
            $department_del_id
        ]);
    }

    $stmt_insert_department = $pdo->prepare("
        INSERT INTO target_department_mapping (
            target_setup_id,
            department_id
        ) VALUES (?, ?)
    ");

    foreach ($department_to_insert as $department_new_id) {
        $stmt_insert_department->execute([
            $target_setup_id,
            $department_new_id
        ]);
    }

    /* ================= DELETE OLD STAFF DETAILS ================= */

    $stmt = $pdo->prepare("
        DELETE FROM target_setup_details
        WHERE target_setup_id = ?
    ");

    $stmt->execute([$target_setup_id]);

    /* ================= INSERT STAFF DETAILS ================= */

    $stmt = $pdo->prepare("
        INSERT INTO target_setup_details (
            target_setup_id,
            staff_id,
            incentive_titles,
            target_counts,
            target_amounts,
            minimum_counts,
            minimum_amounts,
            payout_types,
            ctc_percentages,
            fixed_amounts
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($staff_id as $key => $staff) {

        $payout = trim($payout_type[$key]);

        $percentage = ($payout === 'CTC')
            ? $ctc_percentage[$key]
            : null;

        $fixed = ($payout === 'Fixed')
            ? $fixed_amount[$key]
            : null;

        $target_count_value = trim((string)($target_count[$key] ?? ''));
        $target_amount_value = trim((string)($target_amount[$key] ?? ''));
        $minimum_count_value = trim((string)($minimum_count[$key] ?? ''));
        $minimum_amount_value = trim((string)($minimum_amount[$key] ?? ''));

        $stmt->execute([
            $target_setup_id,
            (int)$staff,
            trim($incentive_title[$key]),
            $target_count_value !== '' ? $target_count_value : null,
            $target_amount_value !== '' ? $target_amount_value : null,
            $minimum_count_value !== '' ? $minimum_count_value : null,
            $minimum_amount_value !== '' ? $minimum_amount_value : null,
            $payout,
            $percentage,
            $fixed
        ]);
    }

    /* ================= COMMIT ================= */

    $pdo->commit();

    echo json_encode([
        "result" => 1,
        "message" => $target_id > 0
            ? "Target Info Updated Successfully!"
            : "Target Info Saved Successfully!",
        "target_setup_id" => $target_setup_id
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "result" => 0,
        "message" => $e->getMessage()
    ]);
}
?>