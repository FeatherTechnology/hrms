<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT ki.id, ki.proof_of, ki.fam_mem, fi.fam_relationship, ki.proof, ki.proof_detail, ki.upload 
                    FROM kyc_info ki 
                   LEFT JOIN family_info fi ON ki.fam_mem = fi.id WHERE ki.id='$id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);