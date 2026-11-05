<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'] ?? 0;
$target_id = (int)($_POST['target_id'] ?? 0);

$detail_ids = $_POST['target_detail_id'] ?? [];
$staff_ids = $_POST['staff_id'] ?? [];
$achievement_basis = $_POST['achievement_basis'] ?? [];
$achieved_values = $_POST['achieved_value'] ?? [];
$incentive_amounts = $_POST['incentive_amount'] ?? [];

try {
    if ($target_id <= 0) {
        throw new Exception("Invalid target setup ID.");
    }

    $total = count($detail_ids);

    if (
        $total === 0 ||
        count($staff_ids) !== $total ||
        count($achievement_basis) !== $total ||
        count($achieved_values) !== $total ||
        count($incentive_amounts) !== $total
    ) {
        throw new Exception("Invalid evaluation details.");
    }

    $pdo->beginTransaction();

    $checkTarget = $pdo->prepare("
        SELECT id
        FROM target_setup
        WHERE id = ?
        FOR UPDATE
    ");
    $checkTarget->execute([$target_id]);

    if (!$checkTarget->fetchColumn()) {
        throw new Exception("Target setup not found.");
    }

    $checkDetail = $pdo->prepare("
        SELECT target_counts, target_amounts
        FROM target_setup_details
        WHERE id = ?
        AND target_setup_id = ?
        AND staff_id = ?
    ");

    $update = $pdo->prepare("
        UPDATE target_setup_details
        SET achievement_basis = ?,
            achieved_value = ?,
            incentive_amount = ?
        WHERE id = ?
        AND target_setup_id = ?
        AND staff_id = ?
    ");

    $processed = [];

    for ($i = 0; $i < $total; $i++) {
        $detail_id = (int)$detail_ids[$i];
        $staff_id = (int)$staff_ids[$i];

        $basis = trim($achievement_basis[$i]);
        $achieved = trim((string)$achieved_values[$i]);
        $incentive = trim((string)$incentive_amounts[$i]);

        if ($detail_id <= 0 || $staff_id <= 0) {
            throw new Exception("Invalid staff evaluation details at row " . ($i + 1) . ".");
        }

        if (isset($processed[$detail_id])) {
            throw new Exception("Duplicate evaluation row found.");
        }

        $processed[$detail_id] = true;

        if (
            $achieved === '' || $incentive === '' ||
            !is_numeric($achieved) || !is_numeric($incentive) ||
            (float)$achieved < 0 || (float)$incentive < 0
        ) {
            throw new Exception("Enter valid evaluation values at row " . ($i + 1) . ".");
        }

        $checkDetail->execute([$detail_id, $target_id, $staff_id]);
        $detail = $checkDetail->fetch(PDO::FETCH_ASSOC);

        if (!$detail) {
            throw new Exception("Staff target detail not found at row " . ($i + 1) . ".");
        }

        $expectedBasis = ($detail['target_counts'] !== null && $detail['target_counts'] !== '')
            ? 'Count'
            : (($detail['target_amounts'] !== null && $detail['target_amounts'] !== '') ? 'Amount' : '');

        if ($expectedBasis === '' || $basis !== $expectedBasis) {
            throw new Exception("Invalid achievement basis for row " . ($i + 1) . ".");
        }

        $update->execute([
            $basis,
            $achieved,
            $incentive,
            $detail_id,
            $target_id,
            $staff_id
        ]);
    }

    // Check whether all staff evaluations are completed
    $checkCompletion = $pdo->prepare("
        SELECT
            COUNT(*) AS total_staff,
            SUM(
                CASE
                    WHEN achieved_value IS NOT NULL
                    AND achieved_value != ''
                    AND incentive_amount IS NOT NULL
                    AND incentive_amount != ''
                    THEN 1
                    ELSE 0
                END
            ) AS completed_staff
        FROM target_setup_details
        WHERE target_setup_id = ?
    ");

    $checkCompletion->execute([$target_id]);
    $completion = $checkCompletion->fetch(PDO::FETCH_ASSOC);

    $status = (
        $completion['total_staff'] > 0 &&
        $completion['total_staff'] == $completion['completed_staff']
    ) ? 2 : 1;

    // Update Target Setup status
    $updateStatus = $pdo->prepare("
        UPDATE target_setup
        SET status = ?,
             update_login_id = ?,
            updated_on = NOW()
        WHERE id = ?
    ");
    $updateStatus->execute([$status, $user_id,$target_id]);

    $pdo->commit();

    echo json_encode([
        'result' => 1,
        'status' => $status,
        'message' => 'Target evaluation submitted successfully.'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'result' => 0,
        'message' => $e->getMessage()
    ]);
}

$pdo = null;
?>