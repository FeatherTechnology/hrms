<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];
$date = date('Y-m-d');

$qry = $pdo->query("UPDATE document_info 
                    SET return_date = '$date' 
                    WHERE id = '$id'");

if ($qry) {
    echo "success";
}
?>