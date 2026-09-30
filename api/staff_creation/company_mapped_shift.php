<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$result = array();

$qry = $pdo->query("
    SELECT id, shift_name, shift_time, start_time, end_time
    FROM shift_creation
    WHERE company_id = '$company_id'
    AND status = 0
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null;

echo json_encode($result);
?>