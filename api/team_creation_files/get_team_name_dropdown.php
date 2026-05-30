<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, team_name FROM team_name_creation WHERE team_status = 0 ORDER BY team_name ASC");

$result = [];

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id' => $row['id'],
        'team_name' => $row['team_name']
    ];
}

echo json_encode($result);

$pdo = null;
