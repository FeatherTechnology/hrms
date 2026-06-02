<?php

/** Department List **
 * Purpose:
 * - Fetches all active departments.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns department data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$department_list_arr = array();

$i = 0;

$stmt = $pdo->prepare("SELECT *
    FROM department_creation
    WHERE department_status = ?
    ORDER BY department_name ASC
");

$stmt->execute([0]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color departmentActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete departmentDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $department_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($department_list_arr);
