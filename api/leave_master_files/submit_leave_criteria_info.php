<?php

/** Leave Save **
 * Purpose:
 * - Checks whether the leave type already exists for the selected company.
 * - Updates an existing leave record when leave_criteria_id is provided.
 * - Inserts a new leave record when leave_criteria_id is empty.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Leave Type Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$company_name      = $_POST['company_name'];
$leave_type        = $_POST['leave_type'];
$no_of_days        = $_POST['no_of_days'];
$leave_criteria_id = $_POST['leave_criteria_id'];
$user_id           = $_SESSION['user_id'];

$result = 0;

/* Check Duplicate Leave Type */
$stmt = $pdo->prepare("SELECT id
    FROM leave_creation
    WHERE REPLACE(TRIM(leave_type), ' ', '') = REPLACE(TRIM(?), ' ', '')
    AND company_id = ?
    AND status = 0
    AND id != ?
");

$stmt->execute([
    $leave_type,
    $company_name,
    $leave_criteria_id ?: 0
]);

if ($stmt->rowCount() > 0) {

    $result = 3; // Already Exists

} else {

    if (!empty($leave_criteria_id)) {

        /* Update Leave */
        $stmt = $pdo->prepare("UPDATE leave_creation
            SET
                company_id = ?,
                leave_type = ?,
                no_of_days = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $company_name,
            $leave_type,
            $no_of_days,
            $user_id,
            $leave_criteria_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }
    } else {

        /* Insert Leave */
        $stmt = $pdo->prepare("INSERT INTO leave_creation
            (
                company_id,
                leave_type,
                no_of_days,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?, ?, NOW()
            )
        ");

        $qry = $stmt->execute([
            $company_name,
            $leave_type,
            $no_of_days,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
