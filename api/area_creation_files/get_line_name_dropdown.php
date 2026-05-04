<?php 
require "../../ajaxconfig.php";
$branch_id = $_POST['branch_id'];

$linename_arr = array();

$result=$pdo->query("SELECT id,linename FROM line_name_creation WHERE branch_id ='$branch_id'");
while( $row = $result->fetch()){
    $id = $row['id'];
    $linename = $row['linename'];
    
    $checkQry=$pdo->query("SELECT * FROM area_creation where status=1 and FIND_IN_SET($id,line_id)");
    if ($checkQry->rowCount()>0){
        $disabled = true;
    }else{
        $disabled=false;
    }

    $linename_arr[] = array("id" => $id, "linename" => $linename,"disabled"=>$disabled);
}

echo json_encode($linename_arr);
?>