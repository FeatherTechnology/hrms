<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$qry = $pdo->query("SELECT cp.max_permission, cw.week_off, cw.week_day
    FROM company_policies cp
    LEFT JOIN company_weekoffs cw ON cp.id = cw.company_policies_id
    WHERE cp.company_id = '$id'
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null;

echo json_encode($result);
