<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$result = 0;
$qry = $pdo->query("SELECT upload FROM `document_info` WHERE id='$id'");
if($qry->rowCount()>0){
    $row = $qry->fetch();
    if ($row['upload']) {
        unlink("../../uploads/loan_issue/doc_info/" . $row['upload']);
        }
    }
    $qry = $pdo->query("DELETE FROM `document_info` WHERE id='$id'");
    if ($qry) {
        $result = 1;
    }else{
        $result = 2;
    }


$pdo = null; //Close connection.

echo json_encode($result);