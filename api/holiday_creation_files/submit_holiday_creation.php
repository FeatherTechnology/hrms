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

$company_id     = $_POST['company_id'];
$from_date      = $_POST['from_date'];
$to_date        = $_POST['to_date'];
$holiday_days   = $_POST['holiday_days'];
$no_of_days     = $_POST['no_of_days'];
$holiday_name   = $_POST['holiday_name'];
$holiday_id     = $_POST['holiday_id'];
$user_id        = $_SESSION['user_id'];

$result = 0;

/* Check Holiday Date Overlap */
$sql = "SELECT id
        FROM holiday_creation
        WHERE company_id = ?
        AND from_date <= ?
        AND to_date >= ?
        AND status = ?";

$params = [
    $company_id,
    $to_date,
    $from_date,
    0
];

/* Ignore current record while updating */
if (!empty($holiday_id)) {
    $sql .= " AND id != ?";
    $params[] = $holiday_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if ($stmt->rowCount() > 0) {
    echo json_encode(3); // Date already exists or overlaps
    exit;
}

if (!empty($holiday_id)) {

    /* Update Holiday */
    $stmt = $pdo->prepare("UPDATE holiday_creation
        SET
            company_id = ?,
            from_date = ?,
            to_date = ?,
            holiday_days = ?,
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
        $holiday_days,
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
            holiday_days,
            no_of_days,
            holiday_name,
            insert_login_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $qry = $stmt->execute([
        $company_id,
        $from_date,
        $to_date,
        $holiday_days,
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
