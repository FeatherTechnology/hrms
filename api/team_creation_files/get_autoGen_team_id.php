<?php
require '../../ajaxconfig.php';
$id = $_POST['id'];

if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT team_code FROM team_name_creation WHERE id = '$id'");
    $qry_info = $qry->fetch();
    $team_ID_final = $qry_info['team_code'];
} else {

    $qry = $pdo->query("SELECT team_code FROM team_name_creation WHERE team_code !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //LID-101
        $l_no = ltrim(strstr($qry_info['team_code'], '-'), '-');
        $l_no = $l_no + 1;
        $team_ID_final = "T-" . "$l_no";
    } else {
        $team_ID_final = "T-" . "101";
    }
}

$pdo = null; // Close Connection

echo json_encode($team_ID_final);
