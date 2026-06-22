<?php
// to get the director details
require "../../ajaxconfig.php";

if (isset($_POST['id']) && $_POST['id'] != '') {
    $id = $_POST['id'];
}

$query = "SELECT * FROM director_creation WHERE  id = :id ";


$stmt = $pdo->prepare($query);

$stmt->bindParam(':id', $id, PDO::PARAM_INT);


$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($result);
