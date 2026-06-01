<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {
      // Check department already used in team_creation
    $checkQry = $pdo->prepare(" SELECT COUNT(*) as cnt  FROM occupation_info  WHERE shift = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // Department already used
        $result = '2';
    } else {
    $qry = $pdo->prepare("UPDATE `shift_creation` SET status = 1 WHERE id = :id");
    $qry->bindParam(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    $result = '1'; // Disabled successfully
    }
} catch (PDOException $e) {
    $result = '0'; // General error
}

$pdo = null; // Close Connection

echo json_encode($result);
