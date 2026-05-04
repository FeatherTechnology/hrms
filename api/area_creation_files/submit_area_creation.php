<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$line_id = $_POST['line_id'];
$area_ids = $_POST['area_id'];
$existing_ids = isset($_POST['area_name2']) ? explode(',', $_POST['area_name2']) : [];

$id = $_POST['id'];
$result = 0; //update

if ($id != '0') {
    $pdo->query("UPDATE `area_creation` SET `branch_id`='$branch_id',`line_id`='$line_id', `status`='1', `update_login_id`='$user_id', `update_on`=NOW() WHERE `id`='$id'");

    // Calculate deleted and newly added IDs
    $area_ids = array_map('intval', $area_ids); // ensure all are integers
    $to_delete = array_diff($existing_ids, $area_ids);
    $to_insert = array_diff($area_ids, $existing_ids);

    // Delete unselected areas
    foreach ($to_delete as $del_id) {
        $pdo->query("DELETE FROM area_creation_area_name WHERE area_creation_id = $id AND area_id = $del_id");
    }

    // Insert new area_ids
    foreach ($to_insert as $new_id) {
        $pdo->query("INSERT INTO area_creation_area_name (area_creation_id, area_id) VALUES ($id, $new_id)");
    }

    if ($pdo) {
        $result = 1; // Update successful
    }
} else {
    $pdo->query("INSERT INTO `area_creation`(`branch_id`, `line_id`, `insert_login_id`, `created_on`) VALUES ('$branch_id','$line_id','$user_id', now())");

    $area_creation_id = $pdo->lastInsertId();
    // Insert into area mapping table
    foreach ($area_ids as $area_id) {
        $area_id = (int)trim($area_id);
        if ($area_id > 0) {
            $areaQry = "INSERT INTO area_creation_area_name (area_creation_id, area_id) VALUES ($area_creation_id, $area_id)";
            $pdo->query($areaQry) or die("Error inserting area map: " . $pdo->errorInfo());
        }
    }

    if ($pdo) {
        $result = 2; // Insert successfull
    }
}

// Close connection
$pdo = null;

echo json_encode($result);