<?php
require "../../../ajaxconfig.php";

$id = $_POST['id'];
$qry = $pdo->query("SELECT * FROM loan_category_creation WHERE id ='$id' ");
if($qry->rowCount()>0){
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo=null;
echo json_encode($response);
?>