<?php
require '../../ajaxconfig.php';

$remark_list_arr = array();
$cus_profile_id = isset($_POST['id']) ? $_POST['id'] : '';
$i=0;
$sub_status = [''=>'',1=>'Consider',2=>'Reject'];
$qry = $pdo->query("SELECT sub_status,remark FROM customer_status WHERE cus_profile_id = '$cus_profile_id'");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $row['sub_status'] = $sub_status[$row['sub_status']];
        $remark_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($remark_list_arr);
$pdo = null; // Close Connection
?>