<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$result = array();
$branch_id = $_POST['branch_id'];

if ($branch_id == '0') {
    $qry = $pdo->query("SELECT bc.id FROM branch_creation bc ");
    if ($qry->rowCount() > 0) {
        $branch = array();
        while ($row1 = $qry->fetch(PDO::FETCH_ASSOC)) {
            $branch[] = $row1['id'];
        }

        $branches = implode(',', $branch);

        $qry1 = $pdo->query("SELECT line_id FROM `area_creation` WHERE FIND_IN_SET(branch_id, '$branches') ");
        if ($qry1->rowCount() > 0) {
            while ($row = $qry1->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row['line_id'];
            }
        }
    }
} else if ($branch_id != '' && $branch_id != '0') {

    $qry1 = $pdo->query("SELECT line_id FROM `area_creation` WHERE FIND_IN_SET(branch_id, $branch_id) ");
    if ($qry1->rowCount() > 0) {
        while ($row = $qry1->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row['line_id'];
        }
    }
}

$pdo = null; //Close Connection.

echo json_encode($result);
