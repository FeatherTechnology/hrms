<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$ctc_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM ctc_creation WHERE company_id = '$company_id' AND status = 0");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Component Classification
        if ($row['component_classification'] == 1) {
            $row['component_classification'] = 'CTC';
        } else if ($row['component_classification'] == 2) {
            $row['component_classification'] = 'NON CTC';
        }

        // Component Category
        if ($row['component_category'] == 1) {
            $row['component_category'] = 'Salary';
        } else if ($row['component_category'] == 2) {
            $row['component_category'] = 'Reimbursement';
        }

        // Pay Frequency
        if ($row['pay_frequency'] == 1) {
            $row['pay_frequency'] = 'Per Month';
        } else if ($row['pay_frequency'] == 2) {
            $row['pay_frequency'] = 'Per Day';
        }

        // Action Button
        $row['action'] = "
            <span class='icon-border_color ctcActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete ctcDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $ctc_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($ctc_list_arr);
