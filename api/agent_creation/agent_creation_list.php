<?php
//Also Using in Loan Entry- Loan Calculation.
require '../../ajaxconfig.php';

$agent_list_arr = array();
$i = 0;
$qry = $pdo->query("SELECT id,agent_code, agent_name, area, occupation,mobile1 FROM agent_creation ");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        $row['action'] = "<span class='icon-border_color agentActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete agentDeleteBtn' value='" . $row['id'] . "'></span>";

        $agent_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($agent_list_arr);
$pdo = null; // Close Connection
