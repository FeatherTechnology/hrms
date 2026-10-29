<?php

/** Poll List **
 * Purpose:
 * - Fetches all poll records for the selected company.
 * - Retrieves mapped department names.
 * - Checks poll validity based on end date/time.
 * - Formats poll date/time for display.
 * - Adds Edit and Delete action buttons for active polls.
 * - Disables actions for expired polls.
 * - Returns poll data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$poll_list_arr = [];

$i = 0;
$selection_tye_arr =[1 =>'Single Selection',2 => 'Multiple Selection'];
// Fetch Polls with Department Names and Answer Count
$stmt = $pdo->prepare("SELECT
        pt.*,
        COUNT(pa.id) AS answer_count,
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
    FROM poll_titles pt
    LEFT JOIN poll_department_mapping pdm ON pt.id = pdm.poll_titles_id
    LEFT JOIN department_creation dc ON dc.id = pdm.department_id
     LEFT JOIN poll_branch_mapping pbm ON pt.id = pbm.poll_titles_id
    LEFT JOIN branch_creation bc ON bc.id = pbm.branch_id
    LEFT JOIN poll_answers pa ON pa.poll_titles_id = pt.id
    WHERE pt.company_id = ?
    AND pt.poll_status != ?
    GROUP BY pt.id
");

$stmt->execute([$company_id, 2]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Store Original Date Time
        $row['start_date_time_original'] = $row['start_date_time'];
        $row['end_date_time_original'] = $row['end_date_time'];

        // Current Date and Month
        $current_datetime = date("Y-m-d H:i:s");
        $current_month = date("Y-m");

        $start_month = !empty($row['start_date_time']) ? date("Y-m", strtotime($row['start_date_time'])): '';
        $end_month = !empty($row['end_date_time']) ? date("Y-m", strtotime($row['end_date_time'])): '';

        // Check Expired
        $is_expired = (!empty($row['end_date_time_original']) && $current_datetime > $row['end_date_time_original']);

        // Hide expired polls if neither date belongs to the current month
        if ( $is_expired && $start_month != $current_month && $end_month != $current_month) {
            continue;
        }

        // Set Poll Status
        if ($is_expired) {
            $row['poll_status'] = 'Time Expired';
        } else {
            $row['poll_status'] = ($row['poll_status'] == 0) ? 'Active' : 'In Active';
        }

        // Format Date Time
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['poll_status'] == 'Time Expired') {
            $row['action'] = "<span class='text-muted'>Expired</span>";
        } elseif ($row['answer_count'] > 0) {
            $row['action'] = "<span class='text-muted'>Active</span>";
        } else {
            $row['action'] = "
            <span class='icon-border_color pollActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete pollDeleteBtn' value='" . $row['id'] . "'></span>
        ";
        }
        $row['selection_type'] = $selection_tye_arr[$row['selection_type']];
        $poll_list_arr[$i] = $row;
        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($poll_list_arr);
