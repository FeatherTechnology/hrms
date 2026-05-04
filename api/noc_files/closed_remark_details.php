<?php
require "../../ajaxConfig.php";
$cp_id = $_POST['cp_id'];
$row = array();
$sub_sts = ['' => '', 1 => 'Consider', 2 => 'Reject'];
$qry = $pdo->query("SELECT sub_status, remark FROM customer_status WHERE cus_profile_id = '$cp_id' ");
if($qry->rowCount()>0){
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    $row['sub_status'] = $sub_sts[$row['sub_status']];
}
echo json_encode($row);
?>