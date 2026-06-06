<?php

/** Holiday Save **
 * Purpose:
 * - Inserts a new holiday record.
 * - Updates an existing holiday record.
 * - Stores holiday date range, number of days, and holiday name.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 */

require '../../ajaxconfig.php';
@session_start();

$company_id   = $_POST['company_id'];
$from_date    = $_POST['from_date'];
$to_date      = $_POST['to_date'];
$no_of_days   = $_POST['no_of_days'];
$holiday_name = $_POST['holiday_name'];
$holiday_id   = $_POST['holiday_id'];
$user_id      = $_SESSION['user_id'];

$result = 0;

if (!empty($holiday_id)) {

    /* Update Holiday */
    $stmt = $pdo->prepare("UPDATE holiday_creation
        SET
            company_id = ?,
            from_date = ?,
            to_date = ?,
            no_of_days = ?,
            holiday_name = ?,
            update_login_id = ?,
            updated_date = NOW()
        WHERE id = ?
    ");

    $qry = $stmt->execute([
        $company_id,
        $from_date,
        $to_date,
        $no_of_days,
        $holiday_name,
        $user_id,
        $holiday_id
    ]);

    if ($qry) {
        $result = 1; // Update Successful
    }
} else {

    /* Insert Holiday */
    $stmt = $pdo->prepare("INSERT INTO holiday_creation
        (
            company_id,
            from_date,
            to_date,
            no_of_days,
            holiday_name,
            insert_login_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?
        )
    ");

    $qry = $stmt->execute([
        $company_id,
        $from_date,
        $to_date,
        $no_of_days,
        $holiday_name,
        $user_id
    ]);

    if ($qry) {
        $result = 2; // Insert Successful
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
