<?php
// to delete the regularization request

require "../../ajaxconfig.php";

$id = $_POST['id'];

$query = "DELETE FROM regularization WHERE id = :id";

$stmt = $pdo->prepare($query);

$stmt->bindParam(':id', $id);

echo $stmt->execute() ? 1 : 0;

?>