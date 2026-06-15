<?php
require '../../ajaxconfig.php';
include "../../moneyFormatIndia.php";
$staff_profile_id = $_POST['staff_profile_id'];
$experience_list_arr = array();
$i = 0;
$experience_type = ['1'=>'Fresher','2'=>'Experienced']; 
$qry = $pdo->query("SELECT * FROM experience_info WHERE staff_profile_id = '$staff_profile_id' ");

if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        $experience_list_arr[$i]['id'] = $row['id'];
        $experience_list_arr[$i]['exp_type'] = !empty($row['exp_type']) ? $experience_type[$row['exp_type']] : '-';
        $experience_list_arr[$i]['total_experience'] = !empty($row['total_experience']) ? $row['total_experience'] : '-';
        $experience_list_arr[$i]['pre_company'] = !empty($row['pre_company']) ? $row['pre_company'] : '-';
        $experience_list_arr[$i]['pre_designation'] = !empty($row['pre_designation']) ? $row['pre_designation'] : '-';
        $experience_list_arr[$i]['work_duration'] = !empty($row['work_duration']) ? $row['work_duration'] : '-';
        $experience_list_arr[$i]['last_salary'] = !empty($row['last_salary']) ? moneyFormatIndia($row['last_salary']) : '-';
        $experience_list_arr[$i]['reason_for_leaving'] = !empty($row['reason_for_leaving']) ? $row['reason_for_leaving'] : '-';

        $action_buttons = "<span class='icon-border_color expActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete expDeleteBtn' value='" . $row['id'] . "'></span>";
        $experience_list_arr[$i]['action'] = $action_buttons;

        $i++;
    }
}

echo json_encode($experience_list_arr);
$pdo = null; // Close Connection
?>