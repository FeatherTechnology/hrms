<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    $pdo->beginTransaction();

    // Delete mapping records first
    $qry = $pdo->prepare("
        DELETE FROM reporting_person_mapping
        WHERE reporting_person_id = :id
    ");
    $qry->bindParam(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    // Delete reporting person
    $qry = $pdo->prepare("
        DELETE FROM reporting_person
        WHERE id = :id
    ");
    $qry->bindParam(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    $pdo->commit();

    $result = '1';

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $result = '0';
}

$pdo = null;

echo json_encode($result);
?>