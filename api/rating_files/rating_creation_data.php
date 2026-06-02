<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT 
        rt.*,
        GROUP_CONCAT(DISTINCT rdm.department_id) AS department_ids

    FROM rating_titles rt
    LEFT JOIN rating_department_mapping rdm ON rt.id = rdm.rating_titles_id
    WHERE rt.id = '$id'
    GROUP BY rt.id
");

$result = [];

if ($qry->rowCount() > 0) {

    $result = $qry->fetch(PDO::FETCH_ASSOC);

    $result['department_ids'] = !empty($result['department_ids'])
        ? explode(',', $result['department_ids'])
        : [];
}

$pdo = null;

echo json_encode($result);
