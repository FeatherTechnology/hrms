<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, addProof_name FROM proof_info");
$proof_list = $qry->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($proof_list);
?>
