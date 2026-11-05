<?php
require '../../ajaxconfig.php';

$suspension_id = $_POST['suspension_id'];

$result = '0'; // Default to failure
$qry = $pdo->prepare("DELETE FROM `staff_suspended` WHERE `id` = :suspension_id");
    $qry->bindParam(':suspension_id', $suspension_id, PDO::PARAM_INT);
    if ($qry->execute()) {
        $result = 1; // Deleted.
    } else {
        throw new Exception();
    }

echo json_encode($result);