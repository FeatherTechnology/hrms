<?php

/** Designation Delete **
 * Purpose:
 * - Checks whether the designation is already used in occupation information.
 * - Prevents deletion if the designation is in use.
 * - Performs a soft delete by updating designation_status = 1.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Delete Successful
 * 2 = Designation Already Mapped
 */

require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    /* Check Designation Usage */
    $checkQry = $pdo->prepare("SELECT COUNT(*) AS cnt
        FROM occupation_info
        WHERE designation = ?
    ");

    $checkQry->execute([$id]);

    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];

    if ($count > 0) {

        $result = 2; // Designation Already Used

    } else {

        /* Soft Delete Designation */
        $stmt = $pdo->prepare("UPDATE designation_creation
            SET designation_status = 1
            WHERE id = ?
        ");

        $qry = $stmt->execute([$id]);

        if ($qry) {
            $result = 1; // Delete Successful
        } else {
            $result = 0; // Failed
        }
    }
} catch (PDOException $e) {

    $result = 0; // Failed
}

$pdo = null; // Close Connection

echo json_encode($result);
