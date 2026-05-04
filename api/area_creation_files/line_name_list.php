<?php 
require "../../ajaxconfig.php";
 $branch_id = $_POST['branch_id'];

$line_name_arr = array();
$qry = $pdo->query("SELECT id,linename FROM line_name_creation WHERE branch_id ='$branch_id' ");
if($qry->rowCount()>0){
    while($linename_info = $qry->fetch(PDO::FETCH_ASSOC)){
        $linename_info['action'] = "<span class='icon-border_color linenameActionBtn' value='" . $linename_info['id'] . "'></span>  <span class='icon-trash-2 linenameDeleteBtn' value='" . $linename_info['id'] . "'></span>";
        $line_name_arr[] = $linename_info;
    }
}

$pdo = null; //Connection Close.

echo json_encode($line_name_arr);


