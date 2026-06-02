<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$rating_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT 
        rt.*,
        GROUP_CONCAT(
            DISTINCT dc.department_name 
            ORDER BY dc.department_name 
            SEPARATOR ', '
        ) AS department_name

    FROM rating_titles rt
    LEFT JOIN rating_department_mapping rdm ON rt.id = rdm.rating_titles_id
    LEFT JOIN department_creation dc ON dc.id = rdm.department_id
    WHERE rt.company_id = '$company_id'
    AND rt.rating_status != 2
    GROUP BY rt.id
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
            $row['rating_status'] = 'Time Expired';
        } else {
            $row['rating_status'] = ($row['rating_status'] == 0) ? 'Active' : 'In Active';
        }

        // Format Date for Display
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['rating_status'] == 'Time Expired') {

            $row['action'] = "<span class='text-muted'>Expired</span>";
        } else {

            $row['action'] = "
        <span class='icon-border_color ratingActionBtn' value='" . $row['id'] . "'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete ratingDeleteBtn' value='" . $row['id'] . "'></span>";
        }

        $rating_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($rating_list_arr);
