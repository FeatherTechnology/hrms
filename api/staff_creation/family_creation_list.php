<?php
require '../../ajaxconfig.php';

$staff_profile_id = $_POST['staff_profile_id'] ?? '';

$family_list_arr = [];
$i = 0;

$stmt = $pdo->prepare("SELECT 
        id,
        fam_name,
        fam_relationship,
        DATE_FORMAT(fam_dob, '%d-%m-%Y') AS fam_dob,
        fam_age,
        fam_mem_sts,
        fam_occupation,
        fam_mobile
    FROM family_info
    WHERE staff_profile_id = :staff_profile_id
");

$stmt->execute([
    ':staff_profile_id' => $staff_profile_id
]);

$status = [1 => 'Living', 2 => 'Deceased'];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $family_list_arr[$i]['id'] = $row['id'];
    $family_list_arr[$i]['fam_name'] = $row['fam_name'];
    $family_list_arr[$i]['fam_relationship'] = $row['fam_relationship'];
    $family_list_arr[$i]['fam_dob'] = $row['fam_dob'];
    $family_list_arr[$i]['fam_age'] = $row['fam_age'];

    // Convert status value to display text
    $family_list_arr[$i]['fam_mem_sts'] = $status[$row['fam_mem_sts']] ?? '';

    $family_list_arr[$i]['fam_occupation'] = $row['fam_occupation'];
    $family_list_arr[$i]['fam_mobile'] = $row['fam_mobile'];

    $action_buttons = "<span class='icon-border_color familyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
    $action_buttons .= "<span class='icon-delete familyDeleteBtn' value='" . $row['id'] . "'></span>";

    $family_list_arr[$i]['action'] = $action_buttons;

    $i++;
}

echo json_encode($family_list_arr);

$pdo = null;
