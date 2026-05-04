<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cheque_no_id = isset($_POST['chequeId']) ? $_POST['chequeId'] : [];
$mort_id = isset($_POST['mortId']) ? $_POST['mortId'] : [];
$endorsement_id = isset($_POST['endorsementId']) ? $_POST['endorsementId'] : [];
$doc_id = isset($_POST['docId']) ? $_POST['docId'] : [];
$gold_id = isset($_POST['goldId']) ? $_POST['goldId'] : [];
$date_of_noc = $_POST['date_of_noc'];
$noc_member = $_POST['noc_member'];
$noc_relation = $_POST['noc_relation'];
$cpid = $_POST['cpid'];
$cus_id = $_POST['cus_id'];
$cheque_list_cnt = $_POST['cheque_list_cnt'];
$mort_list_cnt = $_POST['mort_list_cnt'];
$endorsemnt_list_cnt = $_POST['endorsemnt_list_cnt'];
$doc_list_cnt = $_POST['doc_list_cnt'];
$gold_list_cnt = $_POST['gold_list_cnt'];

foreach ($cheque_no_id as $id) {
    $qry = $pdo->query("UPDATE `cheque_no_list` SET `noc_status`='1',`date_of_noc`='$date_of_noc',`noc_member`='$noc_member',`noc_relationship`='$noc_relation',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
}

foreach ($mort_id as $mid) {
    $qry = $pdo->query("UPDATE `mortgage_info` SET `noc_status`='1',`date_of_noc`='$date_of_noc',`noc_member`='$noc_member',`noc_relationship`='$noc_relation',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$mid'");
}

foreach ($endorsement_id as $eid) {
    $qry = $pdo->query("UPDATE `endorsement_info` SET `noc_status`='1',`date_of_noc`='$date_of_noc',`noc_member`='$noc_member',`noc_relationship`='$noc_relation',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$eid'");
}

foreach ($doc_id as $did) {
    $qry = $pdo->query("UPDATE `document_info` SET `noc_status`='1',`date_of_noc`='$date_of_noc',`noc_member`='$noc_member',`noc_relationship`='$noc_relation',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$did'");
}

foreach ($gold_id as $gid) {
    $qry = $pdo->query("UPDATE `gold_info` SET `noc_status`='1',`date_of_noc`='$date_of_noc',`noc_member`='$noc_member',`noc_relationship`='$noc_relation',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$gid'");
}

$c_qry = $pdo->query("SELECT * FROM cheque_no_list WHERE cus_profile_id = '$cpid' AND noc_status = '1'");
if($c_qry->rowCount() == $cheque_list_cnt){
    $cheque_sts = 2;

} else if($c_qry->rowCount() < $cheque_list_cnt && $c_qry->rowCount() != '0' ){
    $cheque_sts = 1;

} else{
    $cheque_sts = 0;
    
}

$m_qry = $pdo->query("SELECT * FROM mortgage_info WHERE cus_profile_id = '$cpid' AND noc_status = '1'");
if($m_qry->rowCount() == $mort_list_cnt){
    $mort_sts = 2;

} else if($m_qry->rowCount() < $mort_list_cnt && $m_qry->rowCount() != '0' ){
    $mort_sts = 1;

} else{
    $mort_sts = 0;
    
}

$e_qry = $pdo->query("SELECT * FROM endorsement_info WHERE cus_profile_id = '$cpid' AND noc_status = '1'");
if($e_qry->rowCount() == $endorsemnt_list_cnt){
    $endorse_sts = 2;

} else if($e_qry->rowCount() < $endorsemnt_list_cnt && $e_qry->rowCount() != '0' ){
    $endorse_sts = 1;

} else{
    $endorse_sts = 0;
    
}

$d_qry = $pdo->query("SELECT * FROM document_info WHERE cus_profile_id = '$cpid' AND noc_status = '1'");
if($d_qry->rowCount() == $doc_list_cnt){
    $doc_sts = 2;

} else if($d_qry->rowCount() < $doc_list_cnt && $d_qry->rowCount() != '0' ){
    $doc_sts = 1;

} else{
    $doc_sts = 0;
    
}

$g_qry = $pdo->query("SELECT * FROM gold_info WHERE cus_profile_id = '$cpid' AND noc_status = '1'");
if($g_qry->rowCount() == $gold_list_cnt){
    $gold_sts = 2;

} else if($g_qry->rowCount() < $gold_list_cnt && $g_qry->rowCount() != '0' ){
    $gold_sts = 1;

} else{
    $gold_sts = 0;
    
}

if($cheque_sts == '2' && $mort_sts == '2' && $endorse_sts =='2' && $doc_sts =='2' && $gold_sts =='2'){
    $status = '2';
    $pdo->query("UPDATE `customer_status` SET `status`='11',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cpid'");
}else{
    $status = '1';
}

$qry2 = $pdo->query("SELECT id FROM `noc` WHERE `cus_profile_id`='$cpid'");
if($qry2->rowCount()>0){
     // Get the existing ID before update
    $row = $qry2->fetch();
    $last_id = $row['id'];
    $qry = $pdo->query("UPDATE `noc` SET `cus_id`='$cus_id',`cheque_list`='$cheque_sts',`mortgage_list`='$mort_sts',`endorsement_list`='$endorse_sts',`document_list`='$doc_sts',`gold_info`='$gold_sts',`noc_status`='$status',`update_login_id`='$user_id',`updated_on`=now() WHERE `cus_profile_id`='$cpid' ");

}else{
    $qry = $pdo->query("INSERT INTO `noc`( `cus_profile_id`, `cus_id`, `cheque_list`, `mortgage_list`, `endorsement_list`, `document_list`, `gold_info`, `noc_status`,  `insert_login_id`, `created_on`) VALUES ('$cpid','$cus_id','$cheque_sts','$mort_sts','$endorse_sts','$doc_sts','$gold_sts','$status','$user_id',now() )");
    $last_id = $pdo->lastInsertId();
}
    
    $pdo->query("INSERT INTO `noc_ref`( `noc_id`, `date_of_noc`, `noc_member`, `noc_relationship`, `created_on`) VALUES ('$last_id','$date_of_noc','$noc_member','$noc_relation',now())");

$result = 0;
if ($qry) {
    $result = 1;
}

echo json_encode($result);
