<?php
require "../../ajaxconfig.php";
$response = array();
$id = $_POST['id'];
if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT staff_id  FROM staff_creation WHERE staff_id = '$id'");
    $qry_info = $qry->fetch();
    $auto_staff_id = $qry_info['staff_id'];
} else {
    $myStr = 'STF';
    $qry = $pdo->query("SELECT MAX(staff_id) as staff_id FROM staff_creation");
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    if ($row["staff_id"] !='') {
        // If Staff codes exist, generate a new staff code
        $ac2 = $row["staff_id"];
         $number = explode('-', $ac2)[1];
        $new_number = (int)$number + 1;
        $appno2 = str_pad($new_number, 3, '0', STR_PAD_LEFT);
        $auto_staff_id = $myStr . "-" . $appno2;
    } else {
        // If no staff codes exist, set an initial one
        $initialapp = $myStr . "-001";
        $auto_staff_id = $initialapp;
    }
}
$response['staff_id'] = $auto_staff_id;
echo json_encode($response);
?>
