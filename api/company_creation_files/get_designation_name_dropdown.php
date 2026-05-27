<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, designation FROM designation_creation WHERE designation_status = 0 ORDER BY designation ASC");

$result = [];

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id' => $row['id'],
        'designation' => $row['designation']
    ];
}

echo json_encode($result);

$pdo = null;
