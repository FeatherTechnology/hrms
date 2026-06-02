<?php

/** Branch Delete **
 * Purpose:
 * - Checks whether the branch is used in occupation information.
 * - Prevents deletion if the branch is already mapped.
 * - Deletes the branch if it is not in use.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Delete Successful
 * 2 = Branch Already Mapped
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = 0;

try {

    /* Check Branch Usage */
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt
        FROM occupation_info
        WHERE branch_id = ?
    ");

    $stmt->execute([$id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];

    if ($count > 0) {

        $result = 2; // Branch Already Used

    } else {

        /* Delete Branch */
        $stmt = $pdo->prepare("DELETE FROM branch_creation
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
