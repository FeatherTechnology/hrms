<?php
include '../../ajaxconfig.php';

$company_name = $_POST['company_name'];

$result = [];

$qry = $pdo->query(" SELECT pf_applicable,esi_applicable,professional_tax_applicable FROM `statutory_compliance` WHERE  company_id = $company_name  ");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);
$pdo = null;
?>