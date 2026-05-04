<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT pi.id, pi.property, pi.property_detail, pi.property_holder, fi.fam_relationship
    FROM property_info pi
     LEFT JOIN family_info fi ON pi.property_holder = fi.id
    WHERE pi.id='$id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);