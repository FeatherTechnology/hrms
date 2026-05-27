<?php
require '../../ajaxconfig.php';
$id = $_POST['id'];

if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT department_code FROM department_creation WHERE id = '$id'");
    $qry_info = $qry->fetch();
    $department_ID_final = $qry_info['department_code'];
} else {

    $qry = $pdo->query("SELECT department_code FROM department_creation WHERE department_code !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //LID-101
        $l_no = ltrim(strstr($qry_info['department_code'], '-'), '-'); 
        $l_no = $l_no+1;
        $department_ID_final = "D-"."$l_no";
    } else {
        $department_ID_final = "D-" . "101";
    }
}

$pdo = null; // Close Connection

echo json_encode($department_ID_final);
?>
