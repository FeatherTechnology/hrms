<?php

/** Department Save **
 * Purpose:
 * - Checks whether the department name already exists.
 * - Updates an existing department when department_id is provided.
 * - Inserts a new department when department_id is empty.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Department Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$department_code = $_POST['department_code'];
$department_name = $_POST['department_name'];
$department_id   = $_POST['department_id'];
$user_id         = $_SESSION['user_id'];

$result = 0;

/* Check Duplicate Department Name */
$stmt = $pdo->prepare("SELECT id
    FROM department_creation
    WHERE REPLACE(TRIM(department_name), ' ', '') = REPLACE(TRIM(?), ' ', '')
    AND department_status = 0
");

$stmt->execute([$department_name]);

if ($stmt->rowCount() > 0) {

    $result = 3; // Already Exists

} else {

    if (!empty($department_id)) {

        /* Update Department */
        $stmt = $pdo->prepare("UPDATE department_creation
            SET
                department_code = ?,
                department_name = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $department_code,
            $department_name,
            $user_id,
            $department_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }

    } else {

        /* Insert Department */
        $stmt = $pdo->prepare("INSERT INTO department_creation
            (
                department_code,
                department_name,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?, NOW()
            )
        ");

        $qry = $stmt->execute([
            $department_code,
            $department_name,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);