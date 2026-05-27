<?php
require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$shift_info_arr = array();

$i = 0;
$qry = $pdo->query("SELECT id, shift_name, start_time, end_time, shift_time, grace_time FROM shift_creation WHERE company_id = '$company_id' AND status = 0");

if ($qry->rowCount() > 0) {

    $default_live = " ";

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Assign the values to the shift list array
        $shift_info_arr[$i]['id']         = $row['id'];
        $shift_info_arr[$i]['shift_name'] = $row['shift_name'];
        $shift_info_arr[$i]['start_time'] = date("g:i A", strtotime($row['start_time']));
        $shift_info_arr[$i]['end_time']   = date("g:i A", strtotime($row['end_time']));
        $shift_info_arr[$i]['shift_time'] = $row['shift_time'];
        $shift_info_arr[$i]['grace_time'] = $row['grace_time'];

        // Construct action buttons
        $action_buttons = "<span class='icon-border_color shiftInfoActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete shiftInfoDeleteBtn' value='" . $row['id'] . "'></span>";
        $shift_info_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($shift_info_arr);
