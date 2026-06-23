<?php

/** Team List **
 * Purpose:
 * - Fetches active teams based on selected Company.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns team data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$team_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT *
    FROM team_name_creation
    WHERE team_status = ? and company_id = ?
    ORDER BY team_name ASC
");

$stmt->execute([0, $company_id]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color teamActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete teamDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $team_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($team_list_arr);
