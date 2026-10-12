<?php
require "../../ajaxconfig.php";
@session_start();

$userid = $_SESSION['user_id'] ?? '';
try {
    $staff_id = $_POST['staff_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $stmt = $pdo->prepare(" UPDATE staff_creation SET status = $status WHERE id = :id");
    $stmt->execute([':id' => $staff_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 1,
            'message' => ((string)$status === '4')
                ? 'Staff exited successfully.'
                : 'Staff moved to the exit process successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Staff not found or status already changed.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 0,
        'message' => 'Unable to update staff status.'
    ]);
}
