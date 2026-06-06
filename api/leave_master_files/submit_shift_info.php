<?php

/** Shift Save **
 * Purpose:
 * - Inserts a new shift record.
 * - Updates an existing shift record.
 * - Stores shift timing, duration, and grace time details.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 */

require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$shift_name   = $_POST['shift_name'];
$start_time   = date("H:i:s", strtotime($_POST['start_time']));
$end_time     = date("H:i:s", strtotime($_POST['end_time']));
$shift_time   = $_POST['shift_time'];
$grace_time   = $_POST['grace_time'];
$shift_id     = $_POST['shift_id'];
$user_id      = $_SESSION['user_id'];

$result = 0;

if (!empty($shift_id)) {

    /* Update Shift */
    $stmt = $pdo->prepare("UPDATE shift_creation
        SET
            company_id = ?,
            shift_name = ?,
            start_time = ?,
            end_time = ?,
            shift_time = ?,
            grace_time = ?,
            update_login_id = ?,
            updated_date = NOW()
        WHERE id = ?
    ");

    $qry = $stmt->execute([
        $company_name,
        $shift_name,
        $start_time,
        $end_time,
        $shift_time,
        $grace_time,
        $user_id,
        $shift_id
    ]);

    if ($qry) {
        $result = 1; // Update Successful
    }
} else {

    /* Insert Shift */
    $stmt = $pdo->prepare("INSERT INTO shift_creation
        (
            company_id,
            shift_name,
            start_time,
            end_time,
            shift_time,
            grace_time,
            insert_login_id,
            created_date
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, NOW()
        )
    ");

    $qry = $stmt->execute([
        $company_name,
        $shift_name,
        $start_time,
        $end_time,
        $shift_time,
        $grace_time,
        $user_id
    ]);

    if ($qry) {
        $result = 2; // Insert Successful
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
