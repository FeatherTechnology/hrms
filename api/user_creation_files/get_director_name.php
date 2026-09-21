<?php
include '../../ajaxconfig.php';

$result = array();

$qry = $pdo->query("
    SELECT 
        dc.id,
        dc.director_name,
        CASE 
            WHEN u.director_name IS NOT NULL THEN 1
            ELSE 0
        END AS already_exists
    FROM director_creation dc
    LEFT JOIN users u 
        ON u.director_name = dc.id
    ORDER BY dc.director_name
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // close connection

echo json_encode($result);
?>
