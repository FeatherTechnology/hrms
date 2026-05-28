<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT * FROM `license_limits`");

$row = $qry->fetch(PDO::FETCH_ASSOC);

$pdo = null;

echo json_encode([
    'branch_limit'  => $row['branch_limit'] ?? 0,
    'company_limit' => $row['company_limit'] ?? 0
]);