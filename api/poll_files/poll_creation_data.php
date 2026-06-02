<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT 
        pt.*,
        GROUP_CONCAT(DISTINCT pdm.department_id) AS department_ids,
        GROUP_CONCAT(DISTINCT pom.poll_options 
        SEPARATOR '||') AS poll_options

    FROM poll_titles pt
    LEFT JOIN poll_department_mapping pdm ON pt.id = pdm.poll_titles_id
    LEFT JOIN poll_options_mapping pom ON pt.id = pom.poll_titles_id
    WHERE pt.id = '$id'
    GROUP BY pt.id
");

$result = [];

if ($qry->rowCount() > 0) {

    $result = $qry->fetch(PDO::FETCH_ASSOC);

    $result['department_ids'] = !empty($result['department_ids'])
        ? explode(',', $result['department_ids'])
        : [];

    $result['poll_options'] = !empty($result['poll_options'])
        ? explode('||', $result['poll_options'])
        : [];
}

$pdo = null;

echo json_encode($result);
