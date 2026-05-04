<?php
require "../../ajaxconfig.php";

$mort_info_arr = array();
$cus_profile_id = $_POST['cus_profile_id'];
$qry = $pdo->query("SELECT mi.id as m_id, mi.*,     CASE 
            WHEN mi.property_holder_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name,  fi.* FROM mortgage_info mi LEFT JOIN family_info fi ON mi.property_holder_name = fi.id LEFT JOIN customer_profile cp ON mi.cus_profile_id= cp.id WHERE mi.cus_profile_id = '$cus_profile_id' ");
if ($qry->rowCount() > 0) {
    while ($mort_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $mort_info['upload'] = "<a href='uploads/loan_issue/mortgage_info/".$mort_info['upload']."' target='_blank'>".$mort_info['upload']."</a>";
        $mort_info['action'] = "<span class='icon-border_color mortActionBtn' value='" . $mort_info['m_id'] . "'></span> <span class='icon-trash-2 mortDeleteBtn' value='" . $mort_info['m_id'] . "'></span>";
        $mort_info_arr[] = $mort_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($mort_info_arr);