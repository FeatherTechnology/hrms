<?php

/** Department Code Generator **
 * Purpose:
 * - Returns the existing department code when editing a record.
 * - Generates the next department code when creating a new record.
 * - Default starting code: D-101.
 * - Returns department code in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

if (!empty($id) && $id != '0') {

    /* Fetch Existing Department Code */
    $stmt = $pdo->prepare("SELECT department_code
        FROM department_creation
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $departmentInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $department_ID_final = $departmentInfo['department_code'] ?? '';

} else {

    /* Get Latest Department Code */
    $stmt = $pdo->prepare("SELECT department_code
        FROM department_creation
        WHERE department_code != ''
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $departmentInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Example: D-101 => 101
        $lastNumber = ltrim(
            strstr($departmentInfo['department_code'], '-'),
            '-'
        );

        $nextNumber = $lastNumber + 1;

        $department_ID_final = "D-" . $nextNumber;

    } else {

        $department_ID_final = "D-101";
    }
}

$pdo = null; // Close Connection

echo json_encode($department_ID_final);

?>