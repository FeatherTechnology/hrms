<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT 
        cc.*,
        GROUP_CONCAT(DISTINCT cdm.department_id) AS department_ids,
        GROUP_CONCAT(DISTINCT dm.designation_id) AS designation_ids
    FROM company_creation cc
    LEFT JOIN company_department_mapping cdm 
        ON cc.id = cdm.company_id
    LEFT JOIN company_designation_mapping dm 
        ON cc.id = dm.company_id
    WHERE cc.id = '$id'
    GROUP BY cc.id
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close connection.

echo json_encode($result);
