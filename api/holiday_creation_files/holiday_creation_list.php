<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$holiday_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM holiday_creation WHERE company_id = '$company_id' AND status = 0");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Format Dates
        $row['from_date'] = date('d-m-Y', strtotime($row['from_date']));
        $row['to_date'] = date('d-m-Y', strtotime($row['to_date']));

        // Action Button
        $row['action'] = "
            <span class='icon-border_color holidayActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete holidayDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $holiday_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($holiday_list_arr);
