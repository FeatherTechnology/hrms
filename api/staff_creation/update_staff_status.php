<?php
require "../../ajaxconfig.php";
@session_start();

$userid = $_SESSION['user_id'] ?? '';

try {
    $staff_id = $_POST['staff_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $last_wrk_day = $_POST['last_wrk_day'] ?? '';

    if ($staff_id === '' || $status === '') {
        echo json_encode([
            'status' => 0,
            'message' => 'Staff ID and status are required.'
        ]);
        exit;
    }

    // Update staff_creation status
    $stmt = $pdo->prepare("
        UPDATE staff_creation
        SET status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ':status' => $status,
        ':id' => $staff_id
    ]);

    // Update users table when staff is moved to inactive/exit process
    if (in_array((string)$status, ['1', '4'], true)) {

        $userStmt = $pdo->prepare("
            UPDATE users
            SET status = 1,
                relieve_date = :relieve_date,
                update_login_id = :update_login_id,
                updated_on = NOW()
            WHERE staff_name_id = :staff_profile_id
        ");

        $userStmt->execute([
            ':relieve_date' => $last_wrk_day !== '' ? $last_wrk_day : null,
            ':update_login_id' => $userid,
            ':staff_profile_id' => $staff_id
        ]);
    }

    echo json_encode([
        'status' => 1,
        'message' => ((string)$status === '4')
            ? 'Staff exited successfully.'
            : 'Staff moved to the exit process successfully.'
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());

    echo json_encode([
        'status' => 0,
        'message' => 'Unable to update staff status.'
    ]);
}
