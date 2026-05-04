<?php
require '../../ajaxconfig.php';
@session_start();

$addProof_name = $_POST['addProof_name'];
$user_id = $_SESSION['user_id'];
$proof_id = $_POST['proof_id'];

if($proof_id !=''){
    $qry = $pdo->query("UPDATE `proof_info` SET `addProof_name`='$addProof_name',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$proof_id'");
    $result = 0; //update

}else{
    $qry = $pdo->query("INSERT INTO `proof_info`(`addProof_name`,`insert_login_id`,`created_on`) VALUES ('$addProof_name','$user_id',now())");
    $result = 1; //Insert
}

echo json_encode($result);
?>