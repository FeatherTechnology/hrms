<?php

require "../../ajaxconfig.php";

$company_id = $_POST['company_id']; 

if (isset($_POST['designation_level'])) {
    $designation_level = $_POST['designation_level'];
} else {
    $designation_level = '';
}

$designation_level_condition = '';
if ($designation_level != '') {
    $designation_level_condition = "AND designation_level > '$designation_level'";
}

$result = array();

$qry = $pdo->query("
    SELECT id, designation, designation_level
    FROM designation
    WHERE company_id = '$company_id'
    $designation_level_condition
    ORDER BY designation_level ASC
");

if ($qry->rowCount() > 0) {

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

$pdo = null;
