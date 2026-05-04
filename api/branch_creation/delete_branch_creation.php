<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$cnt = '0';
$qry = $pdo->query("SELECT * FROM `users` WHERE FIND_IN_SET('$id' , branch)");
if ($qry->rowCount() > 0) {
    $cnt = '1';
} 

$qry = $pdo->query("SELECT * FROM `area_creation` WHERE branch_id = '$id' ");
if($qry->rowCount()>0){
    $cnt = '2';
}

if($cnt =='1'){
    $result = '2'; // Used in User Creation.

} else if($cnt =='2'){
    $result = '3'; // Used in Area Creation.
    
}else {
    $qry = $pdo->query("DELETE FROM `branch_creation` WHERE `id` = '$id'");
    if ($qry) {
        $result = '1'; // Success
    } else {
        $result = '0'; //Failed
    }
}
$pdo = null; // Close Connection
echo json_encode($result); // Failure