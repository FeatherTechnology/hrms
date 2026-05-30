<?php
require '../../ajaxconfig.php';

$result = [];

$id = $_POST['id'];

$qry = $pdo->query("SELECT 
        tc.*,
        GROUP_CONCAT(DISTINCT tcm.team_id) AS team_ids
    FROM team_creation tc
    LEFT JOIN team_creation_mapping tcm 
        ON tcm.team_creation_id = tc.id
    LEFT JOIN company_creation cc 
        ON tc.company_id = cc.id
    LEFT JOIN department_creation dc 
        ON tc.department_id = dc.id
    WHERE tc.id = '$id'
    GROUP BY tc.id
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null;

echo json_encode($result);
