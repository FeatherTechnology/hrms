<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

$checkQry = $pdo->query("SELECT * FROM area_creation_area_name WHERE area_id = '$id'");

if ($checkQry->rowCount() > 0) {
    $result = 0; //Already added in Area Creation.
} else {
    $qry = $pdo->query("UPDATE `area_name_creation` SET status = 0  WHERE id = '$id'");
    $result = 1; // Disabled
}

$pdo = null; // Close Connection

echo json_encode($result);
