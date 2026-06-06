<?php

/** Poll Answer Save **
 * Purpose:
 * - Stores the employee's poll response.
 * - Saves selected poll option and reason for the selected poll.
 * - Maintains user tracking for submitted responses.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Insert Successful
 */

require '../../ajaxconfig.php';
@session_start();

$poll_titles_id = $_POST['poll_titles_id'];
$poll_value     = $_POST['poll_value'];
$reason         = $_POST['reason'];
$user_id        = $_SESSION['user_id'];

$result = 0;

if (!empty($poll_titles_id)) {

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
