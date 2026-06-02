<?php

/** Department Delete **
 * Purpose:
 * - Checks whether the department is already mapped to any team.
 * - Prevents deletion if the department is in use.
 * - Performs a soft delete by updating department_status = 1.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Delete Successful
 * 2 = Department Already Mapped to Team
 */

require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    /* Check Department Usage */
    $checkQry = $pdo->prepare("SELECT COUNT(*) AS cnt
        FROM team_creation
        WHERE department_id = ?
    ");

    $checkQry->execute([$id]);

    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];

    if ($count > 0) {

        $result = 2; // Department Already Used

    } else {

        /* Soft Delete Department */
        $stmt = $pdo->prepare("UPDATE department_creation
            SET department_status = 1
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
