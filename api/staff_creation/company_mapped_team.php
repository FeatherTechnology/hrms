<?php
require "../../ajaxconfig.php";

$dep_id = $_POST['dep_id'];
$company_id = $_POST['company_id'];
$selected_team = $_POST['selected_team'];

$result = array();

$qry = $pdo->query("
    SELECT DISTINCT
        ti.id,
        ti.team_name
    FROM team_name_creation ti
    LEFT JOIN team_creation_mapping tct 
        ON ti.id = tct.team_id
    LEFT JOIN team_creation tc 
        ON tct.team_creation_id = tc.id
    WHERE ti.team_status = 0
    AND (
        (tc.company_id = '$company_id' AND tc.department_id = '$dep_id')
        OR ti.id = '$selected_team'
    )
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

$pdo = null;
?>