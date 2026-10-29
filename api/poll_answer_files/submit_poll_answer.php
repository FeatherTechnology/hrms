<?php
require '../../ajaxconfig.php';
@session_start();

$poll_titles_id = $_POST['poll_titles_id'];
$poll_value     = $_POST['poll_value']; // Handles single value or comma-separated IDs
$reason         = $_POST['reason'];
$user_id        = $_SESSION['user_id'];

$result = 0;

if (!empty($poll_titles_id) && !empty($poll_value)) {

    $stmt = $pdo->prepare("INSERT INTO poll_answers
        (
            poll_titles_id,
            poll_value,
            reason,
            insert_login_id
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ");

    $qry = $stmt->execute([
        $poll_titles_id,
        $poll_value,
        $reason,
        $user_id
    ]);

    if ($qry) {
        $result = 1; // Insert Successful
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
?>