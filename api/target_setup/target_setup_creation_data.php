<?php
require '../../ajaxconfig.php';
$id = (int)($_POST['id'] ?? 0);

$result = [];

try {
    // Master Details
    $stmt = $pdo->prepare("
        SELECT *
        FROM target_setup
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode([]);
        exit;
    }

    // Branch IDs
    $stmt = $pdo->prepare("
        SELECT branch_id
        FROM target_branch_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$id]);

    $result['branch_ids'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Department IDs
    $stmt = $pdo->prepare("
        SELECT department_id
        FROM target_department_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$id]);

    $result['department_ids'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Staff Target Details
    $stmt = $pdo->prepare("
        SELECT
            id,
            staff_id,
            incentive_titles,
            target_counts,
            target_amounts,
            minimum_counts,
            minimum_amounts,
            payout_types,
            ctc_percentages,
            fixed_amounts,
            achieved_value
        FROM target_setup_details
        WHERE target_setup_id = ?
        ORDER BY id ASC
    ");
    $stmt->execute([$id]);

    $result['staff_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 1,
        'message' => $e->getMessage()
    ]);
}

$pdo = null;
?>