<?php

require '../../ajaxconfig.php';

$department_list_arr = array();

$i = 0;

$qry = $pdo->query("SELECT * FROM department_creation WHERE department_status = 0 ORDER BY department_name ASC");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Action Button
        $row['action'] = "
            <span class='icon-border_color departmentActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
            <span class='icon-delete departmentDeleteBtn' value='" . $row['id'] . "'></span>
        ";

        $department_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null;

echo json_encode($department_list_arr);
