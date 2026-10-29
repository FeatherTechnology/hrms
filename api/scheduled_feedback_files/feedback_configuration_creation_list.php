<?php

/** Feedback Configuration List **
 * Purpose:
 * - Fetches all feedback configuration records for the selected company.
 * - Retrieves mapped department names.
 * - Checks feedback validity based on start and end date/time.
 * - Formats feedback date/time for display.
 * - Adds Edit and Delete action buttons for active feedback configurations.
 * - Disables actions for expired feedback configurations.
 * - Returns feedback data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$feedback_configuration_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT
        ft.*,
        COUNT(ssf.id) AS answer_count,
        GROUP_CONCAT(
            DISTINCT dc.department_name
            ORDER BY dc.department_name
            SEPARATOR ', '
        ) AS department_name,
        GROUP_CONCAT(
            DISTINCT bc.branch_name
            ORDER BY bc.branch_name
            SEPARATOR ', '
        ) AS branch_name
    FROM feedback_titles ft
    LEFT JOIN feedback_department_mapping fdm ON ft.id = fdm.feedback_titles_id
    LEFT JOIN department_creation dc ON dc.id = fdm.department_id
    LEFT JOIN feedback_branch_mapping fbm ON ft.id = fbm.feedback_titles_id
    LEFT JOIN branch_creation bc ON bc.id = fbm.branch_id
    LEFT JOIN staff_sch_feedback ssf ON ssf.feedback_titles_id = ft.id
    WHERE ft.company_id = ?
    AND ft.feedback_status != ?
    GROUP BY ft.id
");

$stmt->execute([$company_id, 2]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Store Original Date Time
        $row['start_date_time_original'] = $row['start_date_time'];
        $row['end_date_time_original']   = $row['end_date_time'];
        // Expired Only When End Date Is Passed
        // Current Date and Month
        $current_datetime = date("Y-m-d H:i:s");
        $current_month = date("Y-m");
        $start_month = !empty($row['start_date_time']) ? date("Y-m", strtotime($row['start_date_time'])): '';
        $end_month = !empty($row['end_date_time']) ? date("Y-m", strtotime($row['end_date_time'])): '';
        // Check Expired
        $is_expired = (
            !empty($row['end_date_time']) &&
            $current_datetime > $row['end_date_time']
        );

        // Hide expired feedback if neither date belongs to current month
        if ( $is_expired && $start_month != $current_month && $end_month != $current_month) {
            continue;
        }

        // Set Feedback Status
        if ($is_expired) {
            $row['feedback_status'] = 'Time Expired';
        } else {
            $row['feedback_status'] = ($row['feedback_status'] == 0) ? 'Active': 'In Active';
        }
        // Format Date Time
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['feedback_status'] == 'Time Expired') {

            $row['action'] = "<span class='text-muted'>Expired</span>";
        } elseif ($row['answer_count'] > 0) {

            $row['action'] = "<span class='text-muted'>Active</span>";
        } else {

            $row['action'] = "
                <span class='icon-border_color FeedbackConfigurationActionBtn' value='" . $row['id'] . "'></span>
                &nbsp;&nbsp;&nbsp;
                <span class='icon-delete FeedbackConfigurationDeleteBtn' value='" . $row['id'] . "'></span>
            ";
        }

        $feedback_configuration_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($feedback_configuration_list_arr);
