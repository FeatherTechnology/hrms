<?php

/** Leave List **
 * Purpose:
 * - Fetches all active leave types for the selected company.
 * - Retrieves leave type and allotted number of days.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns leave data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$leave_info_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT
        id,
        leave_type,
        no_of_days
    FROM leave_creation
    WHERE company_id = ?
    AND status = ?
");

$stmt->execute([$company_id, 0]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $leave_info_arr[$i]['id']         = $row['id'];
        $leave_info_arr[$i]['leave_type'] = $row['leave_type'];
        $leave_info_arr[$i]['no_of_days'] = $row['no_of_days'];

        // Action Button
        $leave_info_arr[$i]['action'] = "
            <span class='icon-border_color leaveInfoActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete leaveInfoDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($leave_info_arr);
