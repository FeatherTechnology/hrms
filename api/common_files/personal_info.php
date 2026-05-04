<?php
require "../../ajaxconfig.php";
$cus_id = $_POST['cus_id'];
$row = '';
$qry = $pdo->query("SELECT MAX(id) as id FROM customer_profile WHERE cus_id = '$cus_id' ");
if($qry->rowCount() > 0){
    $row = $qry->fetchObject();
}
$result = array();
$qry2 = $pdo->query("SELECT cp.cus_id, cp.aadhar_num , cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1, cp.pic, cp.area as area_id, cp.line as line_id, bc.id as branch_id
FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
WHERE cp.id = '$row->id' ");
if ($qry2->rowCount() > 0) {
    $result = $qry2->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null;
echo json_encode($result);
