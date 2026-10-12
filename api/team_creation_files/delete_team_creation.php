<?php
require "../../ajaxconfig.php";

$id = $_POST['id'] ?? '';

try {

    // Validate team ID
    if (empty($id) || !is_numeric($id)) {
        echo json_encode('0');
        exit;
    }

    // Check whether any mapped team name is used in occupation_info
    $check = $pdo->prepare("
        SELECT COUNT(*)
        FROM occupation_info oi
        INNER JOIN team_creation_mapping tcm
            ON tcm.team_id = oi.team
        WHERE tcm.team_creation_id = :id
    ");

    $check->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $check->execute();

    $teamUsed = (int)$check->fetchColumn();

    // If the team is assigned to any staff member, prevent deletion
    if ($teamUsed > 0) {
        echo json_encode('2');
        exit;
    }

    // Team is not used; deactivate it
    $qry = $pdo->prepare("
        UPDATE team_creation
        SET status = 1
        WHERE id = :id
    ");

    $qry->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $qry->execute();

    if ($qry->rowCount() > 0) {
        echo json_encode('1');
    } else {
        echo json_encode('0');
    }

} catch (PDOException $e) {

    error_log("Team deletion error: " . $e->getMessage());
    echo json_encode('0');

} finally {
    $pdo = null;
}
?>
