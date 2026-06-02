<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$feedback_configuration_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT 
        ft.*,
        GROUP_CONCAT(
            DISTINCT dc.department_name 
            ORDER BY dc.department_name 
            SEPARATOR ', '
        ) AS department_name

    FROM feedback_titles ft
    LEFT JOIN feedback_department_mapping fdm ON ft.id = fdm.feedback_titles_id
    LEFT JOIN department_creation dc ON dc.id = fdm.department_id
    WHERE ft.company_id = '$company_id'
    AND ft.feedback_status != 2
    GROUP BY ft.id
");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Store original datetime
        $row['start_date_time_original'] = $row['start_date_time'];
        $row['end_date_time_original']   = $row['end_date_time'];

        // Current Date Time
        $current_datetime = date("Y-m-d H:i:s");

        // Status Check
        $is_expired = !($current_datetime >= $row['start_date_time_original'] && $current_datetime <= $row['end_date_time_original']);

        if ($is_expired) {
            $row['feedback_status'] = 'Time Expired';
        } else {
            $row['feedback_status'] = ($row['feedback_status'] == 0) ? 'Active' : 'In Active';
        }

        // Format Date for Display
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['feedback_status'] == 'Time Expired') {

            $row['action'] = "<span class='text-muted'>Expired</span>";
        } else {

            $row['action'] = "
        <span class='icon-border_color FeedbackConfigurationActionBtn' value='" . $row['id'] . "'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete FeedbackConfigurationDeleteBtn' value='" . $row['id'] . "'></span>";
        }

        $feedback_configuration_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($feedback_configuration_list_arr);
