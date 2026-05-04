<?php
require "../../ajaxconfig.php";
$branch_id = $_POST['branch_id'];

$areaname_arr = array();

$result = $pdo->query("SELECT id, areaname, status FROM area_name_creation WHERE branch_id = '$branch_id' AND status = 1");

while ($row = $result->fetch()) {
    $id = $row['id'];
    $areaname = $row['areaname'];

    // Check if this area_id exists in any active area_creation through the mapping table
    $checkQry = $pdo->query("SELECT *
        FROM area_creation_area_name acan
        JOIN area_creation ac ON ac.id = acan.area_creation_id
        WHERE acan.area_id = $id AND ac.status = 1");

    $disabled = $checkQry->rowCount() > 0;

    $areaname_arr[] = array(
        "id" => $id,
        "areaname" => $areaname,
        "disabled" => $disabled
    );
}

echo json_encode($areaname_arr);

$pdo = null; // Close Connection