<?php
require "../../ajaxconfig.php";

$cheque_info_arr = array();
$cp_id = $_POST['cp_id'];
$qry = $pdo->query("SELECT cnl.id, ci.cus_profile_id, ci.holder_type, ci.holder_name, ci.relationship, ci.bank_name, cnl.cheque_no, cnl.date_of_noc, cnl.noc_member, cnl.noc_relationship, cnl.noc_status 
FROM cheque_info ci 
JOIN cheque_no_list cnl ON ci.id = cnl.cheque_info_id
WHERE ci.cus_profile_id = '$cp_id'");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {

        if ($result['holder_type'] == '1') {
            $holder = 'Customer';
        } else if ($result['holder_type'] == '2') {
            $holder = 'Guarantor';
        } else if ($result['holder_type'] == '3') {
            $holder = 'Family Members';
        } else {
            $holder = '';
        }

        $result['holder_type'] = $holder;
        $result['action'] = "<input type='checkbox' class='noc_cheque_chkbx' name='noc_cheque_chkbx' value='" . $result['id'] . "' data-id='".$result['noc_status']."'>";
        $cheque_info_arr[] = $result;
    }
}

echo json_encode($cheque_info_arr);
