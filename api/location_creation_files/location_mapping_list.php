<?php

require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$staff_profile_id = $_POST['staff_profile_id'];

$location_mapping_arr = array();

$qry = $pdo->query("SELECT lam.id, bc.branch_name, bcs.branch_name AS assigned_branch_name, lam.from_date, lam.to_date, lam.lattitude_longitude , lam.staff_profile_id
        FROM location_access_mapping lam
        LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = lam.staff_profile_id)
        LEFT JOIN branch_creation bc ON oi.branch_id = bc.id 
        LEFT JOIN staff_creation sc ON oi.staff_profile_id = sc.id
        LEFT JOIN branch_creation bcs ON lam.assigned_branch = bcs.id
        WHERE lam.staff_profile_id = '$staff_profile_id' AND lam.status = 0");

if ($qry->rowCount() > 0) {
    while ($location_mapping_info = $qry->fetch(PDO::FETCH_ASSOC)) {

        $current_date = date('Y-m-d');
        $from_date = $location_mapping_info['from_date'];
        $to_date = $location_mapping_info['to_date'];

        // Format Dates
        $location_mapping_info['from_date'] = date('d-m-Y', strtotime($from_date));
        $location_mapping_info['to_date'] = date('d-m-Y', strtotime($to_date));


        // Check expiry
        if ($current_date > $to_date) {
            // Disabled buttons
            $location_mapping_info['action'] = "<span class='icon-border_color text-secondary' style='pointer-events:none; opacity:0.5;'></span> &nbsp;
            <span class='icon-delete text-secondary' style='pointer-events:none; opacity:0.5;'></span>";
        } else {
            // Active buttons
            $location_mapping_info['action'] = "<span class='icon-border_color locationMappingActionBtn' value='" . $location_mapping_info['id'] . "'></span> &nbsp;
            <span class='icon-delete locationMappingDeleteBtn' value='" . $location_mapping_info['id'] . "'></span>";
        }
        $location_mapping_arr[] = $location_mapping_info;
    }
}

$pdo = null;

echo json_encode($location_mapping_arr);
