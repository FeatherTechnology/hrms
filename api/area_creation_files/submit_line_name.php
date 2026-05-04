<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$linename = $_POST['linename'];
$branch_id = $_POST['branch_id'];
$id = $_POST['id'];

$qry = $pdo->query("SELECT * FROM `line_name_creation` WHERE REPLACE(TRIM(linename), ' ', '') = REPLACE(TRIM('$linename'), ' ', '') AND branch_id = '$branch_id' ");
if($qry->rowCount()>0){
    $result = 2; //already Exists.

}else{
    if($id !='0'){
        $pdo->query("UPDATE `line_name_creation` SET `linename`='$linename', `branch_id`='$branch_id',`status`='1',`update_login_id`='$user_id',`updated_on`=now() WHERE `id`='$id'");
        $result = 0; //update
    
    }else{
        $pdo->query("INSERT INTO `line_name_creation`(`linename`, `branch_id`, `insert_login_id`, `created_on`) VALUES ('$linename','$branch_id','$user_id', now())");
        $result = 1; //Insert
    }
}

echo json_encode($result);
?>