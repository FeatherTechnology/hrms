<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];
$line_id = $_POST['lineId'];
$response = array();

//Today
$today_paid_current = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_paid_current, COALESCE(SUM(total_paid_track),0) AS current_todaypaid FROM `collection` c WHERE `coll_sub_status` ='Current' AND DATE(created_date) = CURDATE() ";
$today_paid_pending = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_paid_pending, COALESCE(SUM(total_paid_track),0) AS pending_todaypaid FROM `collection` c WHERE `coll_sub_status` ='Pending' AND DATE(created_date) = CURDATE() ";
$today_paid_od = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_paid_od, COALESCE(SUM(total_paid_track),0) AS od_todaypaid FROM `collection` c WHERE `coll_sub_status` ='OD' AND DATE(created_date) = CURDATE() ";
//Today
$today_penalty_current = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_penalty_current, COALESCE(SUM(penalty_track),0) AS current_todaypenalty FROM `collection` c WHERE `coll_sub_status` ='Current' AND penalty_track != '' AND penalty_track != 0 AND DATE(created_date) = CURDATE() ";
$today_penalty_pending = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_penalty_pending, COALESCE(SUM(penalty_track),0) AS pending_todaypenalty FROM `collection` c WHERE `coll_sub_status` ='Pending' AND penalty_track != '' AND penalty_track != 0 AND DATE(created_date) = CURDATE() ";
$today_penalty_od = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_penalty_od, COALESCE(SUM(penalty_track),0) AS od_todaypenalty FROM `collection` c WHERE `coll_sub_status` ='OD' AND penalty_track != '' AND penalty_track != 0 AND DATE(created_date) = CURDATE() ";
//Today
$today_fine_current = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_fine_current, COALESCE(SUM(coll_charge_track),0) AS current_todayfine FROM `collection` c WHERE `coll_sub_status` ='Current' AND coll_charge_track !='' AND coll_charge_track != 0 AND DATE(created_date) = CURDATE() ";
$today_fine_pending = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_fine_pending, COALESCE(SUM(coll_charge_track),0) AS pending_todayfine FROM `collection` c WHERE `coll_sub_status` ='Pending' AND coll_charge_track !='' AND coll_charge_track != 0 AND DATE(created_date) = CURDATE() ";
$today_fine_od = "SELECT COALESCE(COUNT(`coll_sub_status`),0) AS today_fine_od, COALESCE(SUM(coll_charge_track),0) AS od_todayfine FROM `collection` c WHERE `coll_sub_status` ='OD' AND coll_charge_track !='' AND coll_charge_track != 0  AND DATE(created_date) = CURDATE() ";


if ($line_id != '') {
    //today
    $today_paid_current .= " AND c.line IN($line_id) ";
    $today_paid_pending .= " AND c.line IN($line_id) ";
    $today_paid_od .= " AND c.line IN($line_id) ";
    $today_penalty_current .= " AND c.line IN($line_id) ";
    $today_penalty_pending .= " AND c.line IN($line_id) ";
    $today_penalty_od .= " AND c.line IN($line_id) ";
    $today_fine_current .= " AND c.line IN($line_id) ";
    $today_fine_pending .= " AND c.line IN($line_id) ";
    $today_fine_od .= " AND c.line IN($line_id) ";
} else {
    //today
    $today_paid_current .= " AND c.insert_login_id = '$user_id'";
    $today_paid_pending .= " AND c.insert_login_id = '$user_id'";
    $today_paid_od .= " AND c.insert_login_id = '$user_id'";
    $today_penalty_current .= " AND c.insert_login_id = '$user_id'";
    $today_penalty_pending .= " AND c.insert_login_id = '$user_id'";
    $today_penalty_od .= " AND c.insert_login_id = '$user_id'";
    $today_fine_current .= " AND c.insert_login_id = '$user_id'";
    $today_fine_pending .= " AND c.insert_login_id = '$user_id'";
    $today_fine_od .= " AND c.insert_login_id = '$user_id'";
}

//today
$qry6 = $pdo->query($today_paid_current);
$row6 = $qry6->fetch();
$response['today_paid_current'] = $row6['today_paid_current'];
$response['current_todaypaid'] = $row6['current_todaypaid'];
$qry7 = $pdo->query($today_paid_pending);
$row7 = $qry7->fetch();
$response['today_paid_pending'] = $row7['today_paid_pending'];
$response['pending_todaypaid'] = $row7['pending_todaypaid'];
$qry8 = $pdo->query($today_paid_od);
$row8 = $qry8->fetch();
$response['today_paid_od'] = $row8['today_paid_od'];
$response['od_todaypaid'] = $row8['od_todaypaid'];

$qry9 = $pdo->query($today_penalty_current);
$row9 = $qry9->fetch();
$response['today_penalty_current'] = $row9['today_penalty_current'];
$response['current_todaypenalty'] = $row9['current_todaypenalty'];
$qry10 = $pdo->query($today_penalty_pending);
$row10 = $qry10->fetch();
$response['today_penalty_pending'] = $row10['today_penalty_pending'];
$response['pending_todaypenalty'] = $row10['pending_todaypenalty'];
$qry11 = $pdo->query($today_penalty_od);
$row11 = $qry11->fetch();
$response['today_penalty_od'] = $row11['today_penalty_od'];
$response['od_todaypenalty'] = $row11['od_todaypenalty'];

$qry12 = $pdo->query($today_fine_current);
$row12 = $qry12->fetch();
$response['today_fine_current'] = $row12['today_fine_current'];
$response['current_todayfine'] = $row12['current_todayfine'];
$qry13 = $pdo->query($today_fine_pending);
$row13 = $qry13->fetch();
$response['today_fine_pending'] = $row13['today_fine_pending'];
$response['pending_todayfine'] = $row13['pending_todayfine'];
$qry14 = $pdo->query($today_fine_od);
$row14 = $qry14->fetch();
$response['today_fine_od'] = $row14['today_fine_od'];
$response['od_todayfine'] = $row14['od_todayfine'];

echo json_encode($response);
