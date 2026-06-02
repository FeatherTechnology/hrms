<?php

/** Team Dropdown List **
 * Purpose:
 * - Fetches all active teams.
 * - Returns team ID and team name for dropdown selection.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$result = [];

$stmt = $pdo->prepare("SELECT
        id,
        team_name
    FROM team_name_creation
    WHERE team_status = ?
    ORDER BY team_name ASC
");

$stmt->execute([0]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id'        => $row['id'],
        'team_name' => $row['team_name']
    ];
}

$pdo = null; // Close Connection

echo json_encode($result);
