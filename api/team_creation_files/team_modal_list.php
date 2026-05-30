<?php

require '../../ajaxconfig.php';

$team_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM team_name_creation WHERE team_status = 0 ORDER BY team_name ASC");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color teamActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete teamDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $team_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($team_list_arr);
