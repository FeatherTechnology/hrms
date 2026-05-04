<?php
require "../../ajaxconfig.php";

$endorsement_info_arr = array();
$cp_id = $_POST['cp_id'];
$qry = $pdo->query("SELECT ei.`id`,CASE 
            WHEN ei.owner_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name,ei.`relationship`,ei.`vehicle_details`,ei.`endorsement_name`, ei.`key_original`,ei.`rc_original`, ei.`date_of_noc`, ei.`noc_member`, ei.`noc_relationship`, ei.`noc_status` FROM `endorsement_info` ei LEFT JOIN family_info fi ON ei.owner_name = fi.id LEFT JOIN customer_profile cp ON ei.cus_profile_id= cp.id WHERE ei.`cus_profile_id` ='$cp_id'");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        $result['d_noc'] = '';
        $result['h_person'] = '';
        $result['relation'] = '';
        $result['action'] = "<input type='checkbox' class='noc_endorsement_chkbx' name='noc_endorsement_chkbx' value='" . $result['id'] . "' data-id='".$result['noc_status']."'>";
        $endorsement_info_arr[] = $result;
    }
}

echo json_encode($endorsement_info_arr);
