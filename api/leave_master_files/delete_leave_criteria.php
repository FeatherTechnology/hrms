<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {
    $qry = $pdo->prepare("UPDATE `leave_creation` SET status = 1 WHERE id = :id");
    $qry->bindParam(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    $result = '1'; // Disabled successfully
} catch (PDOException $e) {
    $result = '0'; // General error
}

$pdo = null; // Close Connection

echo json_encode($result);
