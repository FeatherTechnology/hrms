<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];


$qry = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE agent_id = '$id' ");
if($qry->rowCount()>0){
    $result = '2'; // Accesss Denied

}else{
    $qry = $pdo->query("DELETE FROM `agent_creation` WHERE `id` = '$id'");
    if ($qry) {
        $result = '1'; // Success
    } else {
        $result = '0'; //Failed
    }
}

$pdo = null; // Close Connection

echo json_encode($result); // Failure