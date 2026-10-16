<?php
require "../../ajaxconfig.php";

$id = $_POST['id'] ?? '';

try {
    $query = "SELECT req_type, upload FROM regularization WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo 0;
        exit;
    }

    if ($row['req_type'] == 1 && !empty($row['upload'])) {
        $filePath = "../../uploads/leave/document/" . basename($row['upload']);

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $query = "DELETE FROM regularization WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);

    echo $stmt->execute() ? 1 : 0;

} catch (PDOException $e) {
    echo 0;
}
?>
