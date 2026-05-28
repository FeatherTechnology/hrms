<?php

require '../../ajaxconfig.php';

// Retrieve the staff_id and staff_profile_id from POST request
$staff_id = $_POST['staff_id'];
$staffProfileId = $_POST['staff_profile_id'];

try {

    $stmt2 = $pdo->prepare("DELETE FROM staff_creation WHERE staff_id = :staff_id AND id = :staffProfileId");
    $stmt2->execute(['staff_id' => $staff_id, 'staffProfileId' => $staffProfileId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
