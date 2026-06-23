<?php

/** Team Dropdown List **
 * Purpose:
 * - Fetches all active teams.
 * - Returns team ID and team name for dropdown selection.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$result = [];

$stmt = $pdo->prepare("
    SELECT id, team_name
    FROM team_name_creation
    WHERE team_status = ?
      AND company_id = ?
");

$stmt->execute([0, $company_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $id = $row['id'];
    $team_name = $row['team_name'];

    // Check if team is already mapped to an active team_creation
    $checkStmt = $pdo->prepare("
        SELECT 1
        FROM team_creation_mapping tcm
        JOIN team_creation tc
            ON tc.id = tcm.team_creation_id
        WHERE tcm.team_id = ?
          AND tc.status = 0
        LIMIT 1
    ");

    $checkStmt->execute([$id]);

    $disabled = $checkStmt->rowCount() > 0;

    $result[] = [
        "id" => $id,
        "team_name" => $team_name,
        "disabled" => $disabled
    ];
}

$pdo = null;

echo json_encode($result);
