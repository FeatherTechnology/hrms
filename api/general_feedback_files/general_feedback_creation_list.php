<?php

/** General Feedback List **
 * Purpose:
 * - Fetches all general feedback records for the selected company.
 * - Converts status values into display text.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns general feedback data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$general_feedback_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT *
    FROM general_feedback
    WHERE company_id = ?
    AND status != ?
");

$stmt->execute([$company_id, 2]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color generalFeedbackActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete generalFeedbackDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $general_feedback_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($general_feedback_list_arr);
