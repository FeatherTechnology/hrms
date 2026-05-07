<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$result =array();
$qry=$pdo->query("SELECT id, department FROM department  WHERE company_id = '$company_id' ");
if($qry->rowCount()>0){
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null; //Close Connection.

echo json_encode($result);
?>