<?php

require '../../ajaxconfig.php';

$staff_id = $_POST['staff_id'];
$staffProfileId = $_POST['staff_profile_id'];

try {

    $pdo->beginTransaction();

    $tables = [
        'document_info',
        'family_info',
        'qualification_info',
        'experience_info',
        'occupation_info',
        'staff_ctc_info'
    ];

    foreach ($tables as $table) {

        $stmt = $pdo->prepare("DELETE FROM $table WHERE staff_id = :staff_id AND staff_profile_id = :staff_profile_id");

        $stmt->execute([
            'staff_id' => $staff_id,
            'staff_profile_id' => $staffProfileId
        ]);
    }

    // Delete from main table last
    $stmt = $pdo->prepare("DELETE FROM staff_creation WHERE staff_id = :staff_id AND id = :staff_profile_id");

    $stmt->execute([
        'staff_id' => $staff_id,
        'staff_profile_id' => $staffProfileId
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true
    ]);
} catch (PDOException $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
