<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {
    // Check leave type already used in regularization
    $checkQry = $pdo->prepare(" SELECT COUNT(*) as cnt FROM regularization  WHERE leave_type = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // Leave type already used
        $result = '2';
    } else {
        $qry = $pdo->prepare("UPDATE `leave_creation` SET status = 1 WHERE id = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        $qry->execute();

        $result = '1'; // Disabled successfully
    }
} catch (PDOException $e) {
    $result = '0'; // General error
}

$pdo = null; // Close Connection

echo json_encode($result);
