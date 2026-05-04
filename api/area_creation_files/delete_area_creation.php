<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
try {
    $qry = $pdo->query("SELECT * FROM users u JOIN area_creation ac ON FIND_IN_SET(u.line, ac.line_id) WHERE ac.id = '$id' ");
    if ($qry->rowCount() > 0) {
        $result = 2; // Already used in User Creation Table. 
    } else {
        $qry = $pdo->prepare("DELETE FROM `area_creation` WHERE id = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        $qry->execute();
        $result = 1; // Deleted.
    }
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        // Integrity constraint violation
        $result = 0; // Already used in another Table.
    } else {
        // Some other error occurred
        $result = -1; // Indicate a general error.
    }
}

echo json_encode($result);
