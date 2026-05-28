<?php
require '../../ajaxconfig.php';
@session_start();

$company_name = $_POST['company_name'];
$department_name = $_POST['department_name'];
$team_name = $_POST['team_name'];
$team_name2 = isset($_POST['team_name2']) ? explode(',', $_POST['team_name2']) : [];

$user_id = $_SESSION['user_id'];
$team_creation_id = $_POST['team_creation_id'];


if ($team_creation_id != '') {

    $qry = $pdo->query("UPDATE `team_creation` SET `company_id`='$company_name',`department_id`='$department_name',`update_login_id`='$user_id',`updated_date`=now() WHERE `id`='$team_creation_id'");

    // Calculate deleted and newly added IDs
    $team_name = array_map('intval', $team_name);

    $team_to_delete = array_diff($team_name2, $team_name);
    $team_to_insert = array_diff($team_name, $team_name2);

    // Delete unselected treams
    foreach ($team_to_delete as $team_del_id) {
        $pdo->query("DELETE FROM team_creation_mapping WHERE team_creation_id = $team_creation_id AND team_id = $team_del_id");
    }

    // Insert new team_ids
    foreach ($team_to_insert as $team_new_id) {
        $pdo->query("INSERT INTO team_creation_mapping (team_creation_id, team_id) VALUES ($team_creation_id, $team_new_id)");
    }

    if ($pdo) {
        $result = 0; //update successful
    }
} else {

    $qry = $pdo->query("INSERT INTO `team_creation`(`company_id`,`department_id`, `insert_login_id`, `created_date`) VALUES ('$company_name','$department_name','$user_id', now())");

    $team_creation_id = $pdo->lastInsertId();

    // Insert into department mapping table
    foreach ($team_name as $team_id) {
        $team_id = (int)trim($team_id);
        if ($team_id > 0) {
            $teamQry = "INSERT INTO team_creation_mapping (team_creation_id, team_id) VALUES ($team_creation_id, $team_id)";
            $pdo->query($teamQry) or die("Error inserting team map: " . $pdo->errorInfo());
        }
    }

    if ($pdo) {
        $result = 1; //Insert successfull
    }
}

echo json_encode($result);
