<?php
// Get staff and attendance details based on Staff ID or Attendance ID.
require "../../ajaxconfig.php";
session_start();
$userid = $_SESSION['user_id'] ?? "";

$att_con = '';
$att_id = '';

if (isset($_POST['staff_id']) && $_POST['staff_id'] != '') {
    $staff_id = $_POST['staff_id'];
    $stf_con="oi.staff_profile_id =:staff_id";
} else {
    $staff_id = $userid;
    $stf_con ="u.id =:staff_id";
}
if (isset($_POST['att_id']) && $_POST['att_id'] != '') {
    $att_id = $_POST['att_id'];
    $att_con = "AND a.id = '$att_id' ";
}


$query = "SELECT 
    sc.id as stf_id,
    sc.staff_id,
    sc.staff_name,
    sc.staff_type,
    cc.company_name,
    cc.id as cmpy_id,
    bc.branch_name,
    bc.id as brch_id,
    dc.department_name,
    dc.id as dep_id,
    dsc.designation,
    dsc.id as des_id,
    tc.team_name,
    tc.id as team_id,
    a.id as att_id,
    a.entry_time,
    a.reason
    
FROM staff_creation sc

LEFT JOIN occupation_info oi ON oi.id = ( SELECT MAX(id)  FROM occupation_info  WHERE staff_profile_id = sc.id )

LEFT JOIN company_creation cc  ON cc.id = oi.company_id

LEFT JOIN branch_creation bc  ON bc.id = oi.branch_id

LEFT JOIN department_creation dc  ON dc.id = oi.department

LEFT JOIN designation_creation dsc  ON dsc.id = oi.designation

LEFT JOIN team_name_creation tc  ON tc.id = oi.team

LEFT JOIN attendance a ON a.staff_profile_id = sc.id AND a.id = '$att_id'

LEFT JOIN users u ON u.staff_name_id = sc.id

WHERE $stf_con $att_con ";


$stmt = $pdo->prepare($query);

$stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_INT);





$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($result);
