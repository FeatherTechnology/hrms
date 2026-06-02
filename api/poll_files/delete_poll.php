<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    // Check poll already used in poll_answers
    $checkQry = $pdo->prepare("SELECT COUNT(*) as cnt FROM poll_answers WHERE poll_titles_id = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // Poll already used
        $result = '2';
    } else {
        // Soft delete poll
        $qry = $pdo->prepare("UPDATE poll_titles SET poll_status = 2 WHERE id = :id  ");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        $qry->execute();
        $result = '1';
    }

} catch (PDOException $e) {

    $result = '0';
}

$pdo = null;

echo json_encode($result);
?>