<?php
include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (empty($user_id) || !$id || $id < 1) {
    echo 2;
    exit;
}

try {
    $sql = "UPDATE staff_general_feedback SET status = 1,update_login_id = ?,updated_date = NOW() WHERE id = ?  AND status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $id]);

    echo $stmt->rowCount() > 0 ? 1 : 3;

} catch (Throwable $e) {
    error_log('General feedback status update failed: ' . $e->getMessage());
    echo 2;
}
?>
