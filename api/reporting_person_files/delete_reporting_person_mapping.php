<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {
    $pdo->beginTransaction();

    $qry = $pdo->prepare("SELECT reporting_person FROM reporting_person WHERE id = :id");
    $qry->bindValue(':id', $id, PDO::PARAM_INT);
    $qry->execute();
    $reportingPerson = $qry->fetch(PDO::FETCH_ASSOC);

    if (!$reportingPerson) {
        $pdo->rollBack();
        echo json_encode(0);
        exit;
    }

    $staffId = $reportingPerson['reporting_person'];

    if (!empty($staffId)) {
        $qry = $pdo->prepare("DELETE FROM reporting_person_mapping WHERE reporting_staff = :staff_id");
        $qry->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        $qry->execute();
    }

    $qry = $pdo->prepare("DELETE FROM reporting_person_mapping WHERE reporting_person_id = :id");
    $qry->bindValue(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    $qry = $pdo->prepare("DELETE FROM reporting_person WHERE id = :id");
    $qry->bindValue(':id', $id, PDO::PARAM_INT);
    $qry->execute();

    $pdo->commit();

    echo json_encode(1);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode(0);
}

$pdo = null;
?>