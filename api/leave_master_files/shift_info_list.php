<?php

/** Shift List **
 * Purpose:
 * - Fetches all active shifts for the selected company.
 * - Formats start and end times for display.
 * - Adds Edit and Delete action buttons for each shift.
 * - Returns shift data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$shift_info_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT
        id,
        shift_name,
        start_time,
        end_time,
        shift_time,
        grace_time
    FROM shift_creation
    WHERE company_id = ?
    AND status = ?
");

$stmt->execute([$company_id, 0]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $shift_info_arr[$i]['id']         = $row['id'];
        $shift_info_arr[$i]['shift_name'] = $row['shift_name'];
        $shift_info_arr[$i]['start_time'] = date("g:i A", strtotime($row['start_time']));
        $shift_info_arr[$i]['end_time']   = date("g:i A", strtotime($row['end_time']));
        $shift_info_arr[$i]['shift_time'] = $row['shift_time'];
        $shift_info_arr[$i]['grace_time'] = $row['grace_time'];

        // Action Button
        $shift_info_arr[$i]['action'] = "
            <span class='icon-border_color shiftInfoActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete shiftInfoDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($shift_info_arr);
