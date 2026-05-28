<?php
require '../../ajaxconfig.php';
$staff_id = $_POST['staff_id'];
$qualification_list_arr = array();
$i = 0;
$qry = $pdo->query("SELECT * FROM qualification_info WHERE staff_id = '$staff_id' ");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // Construct action buttons
            $qualification_list_arr[$i]['id'] = $row['id']; 
        $qualification_list_arr[$i]['highest_qualification'] = $row['highest_qualification'];
        $qualification_list_arr[$i]['degree'] = $row['degree'];   
        $qualification_list_arr[$i]['specialization'] = $row['specialization'];   
        $qualification_list_arr[$i]['college'] = $row['college'];
        $qualification_list_arr[$i]['university'] = $row['university'];
        $qualification_list_arr[$i]['year_of_passing'] = $row['year_of_passing'];
        $action_buttons = "<span class='icon-border_color qualifyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete qualifyDeleteBtn' value='" . $row['id'] . "'></span>";
        $qualification_list_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

echo json_encode($qualification_list_arr);
$pdo = null; // Close Connection
