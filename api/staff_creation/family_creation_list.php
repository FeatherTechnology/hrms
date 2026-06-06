<?php
require '../../ajaxconfig.php';
$staff_profile_id = $_POST['staff_profile_id'];
$family_list_arr = array();
$i = 0;
$qry = $pdo->query("SELECT id,fam_name, fam_relationship,  DATE_FORMAT(fam_dob, '%d-%m-%Y') as fam_dob, fam_occupation,fam_mobile FROM family_info WHERE staff_profile_id = '$staff_profile_id' ");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Construct action buttons
        $family_list_arr[$i]['id'] = $row['id'];
        $family_list_arr[$i]['fam_name'] = $row['fam_name'];
        $family_list_arr[$i]['fam_relationship'] = $row['fam_relationship'];
        $family_list_arr[$i]['fam_dob'] = $row['fam_dob'];
        $family_list_arr[$i]['fam_occupation'] = $row['fam_occupation'];
        $family_list_arr[$i]['fam_mobile'] = $row['fam_mobile'];
        $action_buttons = "<span class='icon-border_color familyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete familyDeleteBtn' value='" . $row['id'] . "'></span>";
        $family_list_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

echo json_encode($family_list_arr);
$pdo = null; // Close Connection
