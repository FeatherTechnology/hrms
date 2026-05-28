<?php
require '../../ajaxconfig.php';

// Retrieve the staff_profile_id from POST request
$staffProfileId = $_POST['staff_profile_id'];
$staff_id = $_POST['staff_id'];

try {
    // Prepare and execute SQL statement
    $stmt = $pdo->query("SELECT status FROM staff_creation WHERE staff_id = '$staff_id'AND id = '$staffProfileId'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    // Return status as JSON
    echo json_encode($status);

} catch (PDOException $e) {
    // Handle error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>