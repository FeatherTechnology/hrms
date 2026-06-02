<?php

/** Fetch Team Creation Details **
 * Purpose:
 * - Retrieves team creation information based on the provided ID.
 * - Fetches mapped team IDs.
 * - Returns team creation details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$result = [];

$id = $_POST['id'];

$stmt = $pdo->prepare("SELECT
        tc.*,
        GROUP_CONCAT(DISTINCT tcm.team_id) AS team_ids
    FROM team_creation tc
    LEFT JOIN team_creation_mapping tcm
        ON tcm.team_creation_id = tc.id
    LEFT JOIN company_creation cc
        ON tc.company_id = cc.id
    LEFT JOIN department_creation dc
        ON tc.department_id = dc.id
    WHERE tc.id = ?
    GROUP BY tc.id
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
