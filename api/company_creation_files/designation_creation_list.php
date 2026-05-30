<?php

require '../../ajaxconfig.php';

$designation_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM designation_creation WHERE designation_status = 0 ORDER BY designation ASC");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color designationActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete designationDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $designation_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($designation_list_arr);
