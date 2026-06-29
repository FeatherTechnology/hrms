<?php
include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'] ?? '';

$result = [];

$qry = $pdo->query("
    SELECT
        cc.id,
        cc.company_name
    FROM company_creation cc
    JOIN users u ON u.id = '$userid'
    WHERE
        (
            u.user_type = '1'
            OR
            (u.user_type = '2' AND FIND_IN_SET(cc.id, u.company_id))
        )
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);
$pdo = null;
?>

