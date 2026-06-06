<?php

/** Rating Answer Save **
 * Purpose:
 * - Stores the employee's rating response.
 * - Saves rating value and reason for the selected rating title.
 * - Maintains user tracking for submitted responses.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Insert Successful
 */

require '../../ajaxconfig.php';
@session_start();

$rating_titles_id = $_POST['rating_titles_id'];
$rating_value     = $_POST['rating_value'];
$reason           = $_POST['reason'];
$user_id          = $_SESSION['user_id'];

$result = 0;

if (!empty($rating_titles_id)) {

    $stmt = $pdo->prepare("INSERT INTO rating_answers
        (
            rating_titles_id,
            rating_value,
            reason,
            insert_login_id
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ");

    $qry = $stmt->execute([
        $rating_titles_id,
        $rating_value,
        $reason,
        $user_id
    ]);

    if ($qry) {
        $result = 1; // Insert Successful
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
