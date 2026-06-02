<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    // Check rating already used in rating_answers
    $checkQry = $pdo->prepare("SELECT COUNT(*) as cnt FROM rating_answers WHERE rating_titles_id = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // Rating already used
        $result = '2';
    } else {
        // Soft delete rating
        $qry = $pdo->prepare("UPDATE rating_titles SET rating_status = 2 WHERE id = :id  ");
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