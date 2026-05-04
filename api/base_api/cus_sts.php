<?php
require '../../ajaxconfig.php';

// Retrieve the cus_profile_id from POST request
$custProfileId = $_POST['cus_profile_id'];
$cus_id = $_POST['cus_id'];

try {
    // Prepare and execute SQL statement
    $stmt = $pdo->query("SELECT status FROM customer_status WHERE cus_id='$cus_id'AND cus_profile_id = '$custProfileId'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    // Return status as JSON
    echo json_encode($status);

} catch (PDOException $e) {
    // Handle error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>