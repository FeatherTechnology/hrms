<?php
require "../../ajaxconfig.php";

$id = $_POST['id'];

$result = 0; // initial.
$qry = $pdo->query("SELECT * FROM  loan_category_creation WHERE FIND_IN_SET($id, scheme_name)");
if ($qry->rowCount() > 0) {
    $result = 1; // Already Used.
} else {
    $qry = $pdo->query("DELETE FROM `scheme` WHERE id = '$id'");
    if ($qry) {
        $result = 2; // Deleted.
    }
}

echo json_encode($result);
