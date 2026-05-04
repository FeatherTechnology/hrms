<?php
require "../../ajaxconfig.php";
$result = 1;
$line_id = $_POST['lineID'];
$qry = $pdo->query("SELECT * FROM users WHERE FIND_IN_SET('$line_id', line) ");
if($qry->rowCount()>0){
    $acqry = $pdo->query("SELECT * FROM area_creation WHERE line_id =  '$line_id' ");
    if($acqry->rowCount() ==1){
        $result = 0; //Access Denied
    }
}
$pdo=null;
echo json_encode($result);
?>