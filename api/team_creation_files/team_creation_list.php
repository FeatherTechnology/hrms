<?php
require '../../ajaxconfig.php';

$team_creation_list_arr = array();

$qry = $pdo->query("SELECT 
        tc.id,
        cc.company_name,
        dc.department_name,
        GROUP_CONCAT(
            DISTINCT tnc.team_name 
            ORDER BY tnc.team_name 
            SEPARATOR ', '
        ) AS team_name
    FROM team_creation tc
    LEFT JOIN company_creation cc ON cc.id = tc.company_id
    LEFT JOIN department_creation dc ON dc.id = tc.department_id
    LEFT JOIN team_creation_mapping tcm ON tcm.team_creation_id = tc.id
    LEFT JOIN team_name_creation tnc ON tnc.id = tcm.team_id
    WHERE tnc.team_status = 0 AND tc.status = 0
    GROUP BY tc.id
");

if ($qry->rowCount() > 0) {

    while ($teamCreationInfo = $qry->fetch(PDO::FETCH_ASSOC)) {

        $teamCreationInfo['action'] =
            "<span class='icon-border_color teamCreationActionBtn' value='" . $teamCreationInfo['id'] . "'></span>
             <span class='icon-trash-2 teamCreationDeleteBtn' value='" . $teamCreationInfo['id'] . "'></span>";

        $team_creation_list_arr[] = $teamCreationInfo;
    }
}

$pdo = null;

echo json_encode($team_creation_list_arr);
