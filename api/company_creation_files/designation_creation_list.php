<?php

/** Designation List **
 * Purpose:
 * - Fetches all active designations.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns designation data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$designation_list_arr = array();

$i = 0;

$stmt = $pdo->prepare("SELECT * FROM designation_creation WHERE designation_status = ? ORDER BY designation ASC");

$stmt->execute([0]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color designationActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete designationDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $designation_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($designation_list_arr);
