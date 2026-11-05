<?php
require '../../ajaxconfig.php';

$target_id = (int)($_POST['target_id'] ?? 0);
$detail_ids = $_POST['target_detail_id'] ?? [];
$staff_ids = $_POST['staff_id'] ?? [];
$final_incentives = $_POST['final_incentive'] ?? [];

try {
    if ($target_id <= 0) {
        throw new Exception("Invalid target setup ID.");
    }

    $total = count($detail_ids);

    if (
        $total === 0 ||
        count($staff_ids) !== $total ||
        count($final_incentives) !== $total
    ) {
        throw new Exception("Invalid target incentive details.");
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
        SELECT achieved_value, final_incentive
        FROM target_setup_details
        WHERE id = ?
        AND target_setup_id = ?
        AND staff_id = ?
        FOR UPDATE
    ");

    $update = $pdo->prepare("
        UPDATE target_setup_details
        SET final_incentive = ?
        WHERE id = ?
        AND target_setup_id = ?
        AND staff_id = ?
    ");

    $processed = [];

    for ($i = 0; $i < $total; $i++) {
        $detail_id = (int)$detail_ids[$i];
        $staff_id = (int)$staff_ids[$i];
        $final_incentive = trim((string)$final_incentives[$i]);
        $processed[$detail_id] = true;
        $checkDetail->execute([$detail_id, $target_id, $staff_id]);
        $detail = $checkDetail->fetch(PDO::FETCH_ASSOC);
        $update->execute([
            $final_incentive,
            $detail_id,
            $target_id,
            $staff_id
        ]);

        if ($update->rowCount() !== 1) {
            throw new Exception("Unable to save Final Incentive at row " . ($i + 1) . ".");
        }
    }

    $pdo->commit();

    echo json_encode([
        'result' => 1,
        'message' => 'Final Incentive submitted successfully.'
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