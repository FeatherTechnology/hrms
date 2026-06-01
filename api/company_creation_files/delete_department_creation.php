<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {

    // Check department already used in team_creation
    $checkQry = $pdo->prepare(" SELECT COUNT(*) as cnt FROM team_creation  WHERE department_id = :id  ");
    $checkQry->bindParam(':id', $id, PDO::PARAM_INT);
    $checkQry->execute();
    $count = $checkQry->fetch(PDO::FETCH_ASSOC)['cnt'];
    if ($count > 0) {
        // Department already used
        $result = '2';
    } else {
        // Soft delete department
        $qry = $pdo->prepare("  UPDATE department_creation SET department_status = 1  WHERE id = :id  ");
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