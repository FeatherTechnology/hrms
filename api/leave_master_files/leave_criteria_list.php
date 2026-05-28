<?php
require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$leave_info_arr = array();

$i = 0;
$qry = $pdo->query("SELECT id, leave_type, no_of_days FROM leave_creation WHERE company_id = '$company_id' AND status = 0");

if ($qry->rowCount() > 0) {

    $default_live = " ";

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Assign the values to the family list array
        $leave_info_arr[$i]['id'] = $row['id'];
        $leave_info_arr[$i]['leave_type'] = $row['leave_type'];
        $leave_info_arr[$i]['no_of_days'] = $row['no_of_days'];

        // Construct action buttons
        $action_buttons = "<span class='icon-border_color leaveInfoActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete leaveInfoDeleteBtn' value='" . $row['id'] . "'></span>";
        $leave_info_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($leave_info_arr);
