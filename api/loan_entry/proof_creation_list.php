<?php
require '../../ajaxconfig.php';

$proof_list_arr = array();
$i=0;
$qry = $pdo->query("SELECT id,addProof_name FROM proof_info ");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
  
    $row['action'] = "<span class='icon-border_color proofActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete proofDeleteBtn' value='" . $row['id'] . "'></span>";

        $proof_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($proof_list_arr);
$pdo = null; // Close Connection
?>