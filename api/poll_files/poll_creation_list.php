<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$poll_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT 
        pt.*,
        GROUP_CONCAT(
            DISTINCT dc.department_name 
            ORDER BY dc.department_name 
            SEPARATOR ', '
        ) AS department_name

    FROM poll_titles pt
    LEFT JOIN poll_department_mapping pdm ON pt.id = pdm.poll_titles_id
    LEFT JOIN department_creation dc ON dc.id = pdm.department_id
    WHERE pt.company_id = '$company_id'
    AND pt.poll_status != 2
    GROUP BY pt.id
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
            $row['poll_status'] = 'Time Expired';
        } else {
            $row['poll_status'] = ($row['poll_status'] == 0) ? 'Active' : 'In Active';
        }

        // Format Date for Display
        $row['start_date_time'] = !empty($row['start_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['start_date_time']))
            : '';

        $row['end_date_time'] = !empty($row['end_date_time'])
            ? date("d-m-Y g:i A", strtotime($row['end_date_time']))
            : '';

        // Action Button
        if ($row['poll_status'] == 'Time Expired') {

            $row['action'] = "<span class='text-muted'>Expired</span>";
        } else {

            $row['action'] = "
        <span class='icon-border_color pollActionBtn' value='" . $row['id'] . "'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete pollDeleteBtn' value='" . $row['id'] . "'></span>";
        }

        $poll_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($poll_list_arr);
