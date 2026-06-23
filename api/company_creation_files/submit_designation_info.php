<?php

/** Designation Save **
 * Purpose:
 * - Checks whether the designation already exists.
 * - Updates an existing designation when designation_id is provided.
 * - Inserts a new designation when designation_id is empty.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Designation Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$designation       = $_POST['designation'];
$designation_level = $_POST['designation_level'];
$designation_id    = $_POST['designation_id'];
$user_id           = $_SESSION['user_id'];

$result = 0;

// Common duplicate check
$condition = ($designation_id != '') ? "AND id != '$designation_id'" : "";
    if (!empty($designation_id)) {

        /* Update Designation */
        $stmt = $pdo->prepare("UPDATE designation_creation
            SET
                designation = ?,
                designation_level = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $designation,
            $designation_level,
            $user_id,
            $designation_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }
    } else {

        /* Insert Designation */
        $stmt = $pdo->prepare("INSERT INTO designation_creation
            (
                designation,
                designation_level,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?, NOW()
            )
        ");

        $qry = $stmt->execute([
            $designation,
            $designation_level,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }


$pdo = null; // Close Connection

echo json_encode($result);
