<?php

/** Designation Dropdown List **
 * Purpose:
 * - Fetches all active designations.
 * - Returns designation data for dropdown selection.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$result = [];

$stmt = $pdo->prepare("SELECT
        id,
        designation
    FROM designation_creation
    WHERE designation_status = ?
    ORDER BY designation ASC
");

$stmt->execute([0]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id'          => $row['id'],
        'designation' => $row['designation']
    ];
}

$pdo = null; // Close Connection

echo json_encode($result);
