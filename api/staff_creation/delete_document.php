<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = '0'; // Default to failure
  $qry = $pdo->prepare("SELECT upload FROM document_info WHERE id = :id");
   $qry->bindParam(':id', $id, PDO::PARAM_INT);
        if ($qry->execute()) {
            $row = $qry->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $filePath = "../../uploads/staff_creation/document/" . $row['upload'];
                if (is_file($filePath)) {
                    unlink($filePath);
                }

                $deleteQry = $pdo->prepare("DELETE FROM document_info WHERE id = :id");
                $deleteQry->bindParam(':id', $id, PDO::PARAM_INT);
                if ($deleteQry->execute()) {
                    $result = '1'; // Success
                }
            }
        }
$pdo = null; // Close Connection

echo json_encode($result);