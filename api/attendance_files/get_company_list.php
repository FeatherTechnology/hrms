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
    JOIN users u
    WHERE u.id = '$userid'
    AND (
        CASE
            WHEN u.user_type = '1'
            THEN FIND_IN_SET(cc.id, u.director_company)
            ELSE FIND_IN_SET(cc.id, u.company_id)
        END
    )
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);
$pdo = null;
?>