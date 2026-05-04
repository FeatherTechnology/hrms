<?php
require "../../ajaxconfig.php";

$cp_id = $_POST['cp_id'];

$result = array();
$qry = $pdo->query("SELECT * FROM collection_charges WHERE cus_profile_id = '$cp_id' AND coll_date != '' ");
if ($qry->rowCount() > 0) {
    while($fineInfo =  $qry->fetch(PDO::FETCH_ASSOC)){
        $fineInfo['coll_date'] = date('d-m-Y', strtotime($fineInfo['coll_date']));
        $result[] = $fineInfo;
    }
}
$pdo = null; //Close Connection.
echo json_encode($result);
