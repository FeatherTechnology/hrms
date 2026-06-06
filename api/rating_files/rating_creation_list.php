<?php

/** Rating List **
 * Purpose:
 * - Fetches all rating records for the selected company.
 * - Retrieves mapped department names.
 * - Checks rating validity based on end date/time.
 * - Formats rating date/time for display.
 * - Adds Edit and Delete action buttons for active ratings.
 * - Disables actions for expired ratings.
 * - Returns rating data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$rating_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT
        rt.*,
        COUNT(ra.id) AS answer_count,
        GROUP_CONCAT(
            DISTINCT dc.department_name
            ORDER BY dc.department_name
            SEPARATOR ', '
        ) AS department_name
    FROM rating_titles rt
    LEFT JOIN rating_department_mapping rdm ON rt.id = rdm.rating_titles_id
    LEFT JOIN department_creation dc ON dc.id = rdm.department_id
    LEFT JOIN rating_answers ra ON ra.rating_titles_id = rt.id
    WHERE rt.company_id = ?
    AND rt.rating_status != ?
    GROUP BY rt.id
");

$stmt->execute([$company_id, 2]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Store Original Date Time
        $row['start_date_time_original'] = $row['start_date_time'];
        $row['end_date_time_original']   = $row['end_date_time'];

        // Current Date Time
        $current_datetime = date("Y-m-d H:i:s");

        // Expired Only When End Date Is Passed
        $is_expired = ($current_datetime > $row['end_date_time_original']);

        if ($is_expired) {

            $row['rating_status'] = 'Time Expired';
        } else {

            $row['rating_status'] = ($row['rating_status'] == 0) ? 'Active' : 'In Active';
        }

        // Format Date Time
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['rating_status'] == 'Time Expired') {

            $row['action'] = "<span class='text-muted'>Expired</span>";
        } elseif ($row['answer_count'] > 0) {

            $row['action'] = "<span class='text-muted'>Answered</span>";
        } else {

            $row['action'] = "
                <span class='icon-border_color ratingActionBtn' value='" . $row['id'] . "'></span>
                &nbsp;&nbsp;&nbsp;
                <span class='icon-delete ratingDeleteBtn' value='" . $row['id'] . "'></span>
            ";
        }

        $rating_list_arr[$i] = $row;
        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($rating_list_arr);
