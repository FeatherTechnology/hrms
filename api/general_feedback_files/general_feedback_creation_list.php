<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$general_feedback_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM general_feedback WHERE company_id = '$company_id' AND status != 2");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Status
        if ($row['status'] == 0) {
            $row['status'] = 'Active';
        } else if ($row['status'] == 1) {
            $row['status'] = 'In Active';
        }

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

$pdo = null;

echo json_encode($general_feedback_list_arr);
