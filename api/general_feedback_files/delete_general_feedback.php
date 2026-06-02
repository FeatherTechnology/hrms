<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    // Check general feedback already used in staff_general_feedback
    $checkQry = $pdo->prepare("SELECT COUNT(*) as cnt FROM staff_general_feedback WHERE general_feedback_id = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // General feedback already used
        $result = '2';
    } else {
        // Soft delete general feedback
        $qry = $pdo->prepare("UPDATE general_feedback SET status = 2 WHERE id = :id  ");
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