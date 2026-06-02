<?php

/** Team Code Generator **
 * Purpose:
 * - Returns the existing team code when editing a record.
 * - Generates the next team code when creating a new record.
 * - Default starting code: T-101.
 * - Returns team code in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

if (!empty($id) && $id != '0') {

    /* Fetch Existing Team Code */
    $stmt = $pdo->prepare("SELECT team_code
        FROM team_name_creation
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $team_ID_final = $teamInfo['team_code'] ?? '';
} else {

    /* Get Latest Team Code */
    $stmt = $pdo->prepare("SELECT team_code
        FROM team_name_creation
        WHERE team_code != ''
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Example: T-101 => 101
        $lastNumber = ltrim(
            strstr($teamInfo['team_code'], '-'),
            '-'
        );

        $nextNumber = $lastNumber + 1;

        $team_ID_final = "T-" . $nextNumber;
    } else {

        $team_ID_final = "T-101";
    }
}

$pdo = null; // Close Connection

echo json_encode($team_ID_final);
