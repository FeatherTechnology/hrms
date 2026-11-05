<?php
require "../../ajaxconfig.php";

$id = (int)($_POST['id'] ?? 0);
$result = '0';

try {
    if ($id <= 0) {
        throw new Exception("Invalid Target Setup ID.");
    }

    $pdo->beginTransaction();

    // Check whether evaluation has started
    $check = $pdo->prepare("
        SELECT COUNT(*)
        FROM target_setup_details
        WHERE target_setup_id = ?
        AND achieved_value IS NOT NULL
        AND achieved_value != ''
    ");
    $check->execute([$id]);

    if ((int)$check->fetchColumn() > 0) {
        throw new Exception("Target evaluation has already started. Deletion is not allowed.");
    }

    // Check whether Target Setup exists
    $check = $pdo->prepare("SELECT id FROM target_setup WHERE id = ?");
    $check->execute([$id]);

    if (!$check->fetchColumn()) {
        throw new Exception("Target Setup not found.");
    }

    // Delete staff target details
    $stmt = $pdo->prepare("
        DELETE FROM target_setup_details
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$id]);

    // Delete branch mappings
    $stmt = $pdo->prepare("
        DELETE FROM target_branch_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$id]);

    // Delete department mappings
    $stmt = $pdo->prepare("
        DELETE FROM target_department_mapping
        WHERE target_setup_id = ?
    ");
    $stmt->execute([$id]);

    // Delete Target Setup master
    $stmt = $pdo->prepare("
        DELETE FROM target_setup
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $pdo->commit();
    $result = '1';

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Target Setup Delete Error: " . $e->getMessage());
    $result = '0';
}

$pdo = null;
echo json_encode($result);
?>