<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

try {
    $qry = $pdo->query("SELECT * FROM users WHERE FIND_IN_SET('$id', loan_category)");
    if($qry->rowCount()>0){
        $result = '2'; // Already used in User Creation Table. 
    }else{
        $qry = $pdo->prepare("DELETE FROM `loan_category_creation` WHERE id = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        $qry->execute();
        $result = '0'; // Deleted.
    }
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        // Integrity constraint violation
        $result = '1'; // Already used in Another Table.
    } else {
        // Some other error occurred
        $result = '-1'; // Indicate a general error.
    }
}

echo json_encode($result);
