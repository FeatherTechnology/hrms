<?php

/** Holiday List **
 * Purpose:
 * - Fetches all active holidays for the selected company.
 * - Formats holiday dates for display.
 * - Disables edit/delete actions for expired holidays.
 * - Adds Edit and Delete action buttons for active holidays.
 * - Returns holiday data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$holiday_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT * FROM holiday_creation WHERE company_id = ? AND status = ?");

$stmt->execute([$company_id, 0]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Format Dates
        $current_date = date('Y-m-d');
        $to_date = $row['to_date'];

        $row['from_date'] = date('d-m-Y', strtotime($row['from_date']));
        $row['to_date']   = date('d-m-Y', strtotime($row['to_date']));

        // Action Button
        if ($current_date > $to_date) {

            $row['action'] = "
                <span class='icon-border_color text-secondary' style='pointer-events:none; opacity:0.5;'></span>
                &nbsp;
                <span class='icon-delete text-secondary' style='pointer-events:none; opacity:0.5;'></span>
            ";
        } else {

            $row['action'] = "
                <span class='icon-border_color holidayActionBtn' value='" . $row['id'] . "'></span>
                &nbsp;&nbsp;&nbsp;
                <span class='icon-delete holidayDeleteBtn' value='" . $row['id'] . "'></span>
            ";
        }

        $holiday_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($holiday_list_arr);
