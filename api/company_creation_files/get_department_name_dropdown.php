<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, department_name FROM department_creation WHERE department_status = 0 ORDER BY department_name ASC");

$result = [];

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id' => $row['id'],
        'department_name' => $row['department_name']
    ];
}

echo json_encode($result);

$pdo = null;
