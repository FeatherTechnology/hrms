<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$staff_profile_id = $_POST['staff_profile_id'];
$staff_id = $_POST['staff_id'];

try {
    $qry = $pdo->query("SELECT * FROM qualification_info WHERE staff_profile_id = '$staff_profile_id' ");
    if ($qry->rowCount() == 1 && $staff_profile_id != '') { //If Only one count of qualification for the staff then restrict to delete.
        $result = '0';
    } else {
        $qry = $pdo->prepare("DELETE FROM `qualification_info` WHERE `id` = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        if ($qry->execute()) {
            $result = 1; // Deleted.
        } else {
            throw new Exception();
        }
    }
} catch (Exception $e) {
    $result = 2; // Handle general exceptions
}

echo json_encode($result);
