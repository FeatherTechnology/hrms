<?php
require "../../ajaxconfig.php";

$endorse_info_arr = array();
$cus_profile_id = $_POST['cus_profile_id'];
$qry = $pdo->query("SELECT ei.id as e_id, ei.*,   CASE 
            WHEN ei.owner_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name, fi.* FROM endorsement_info ei LEFT JOIN family_info fi ON ei.owner_name = fi.id LEFT JOIN customer_profile cp ON ei.cus_profile_id= cp.id WHERE ei.cus_profile_id = '$cus_profile_id' ");
if ($qry->rowCount() > 0) {
    while ($endorse_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $endorse_info['upload'] = "<a href='uploads/loan_issue/endorsement_info/".$endorse_info['upload']."' target='_blank'>".$endorse_info['upload']."</a>";
        $endorse_info['action'] = "<span class='icon-border_color endorseActionBtn' value='" . $endorse_info['e_id'] . "'></span> <span class='icon-trash-2 endorseDeleteBtn' value='" . $endorse_info['e_id'] . "'></span>";
        $endorse_info_arr[] = $endorse_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($endorse_info_arr);