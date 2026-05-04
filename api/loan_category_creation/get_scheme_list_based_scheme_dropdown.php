<?php
require "../../ajaxconfig.php";

$scheme_id = $_POST['scheme_id'];
$scheme_arr = array();
$qry = $pdo->query("SELECT `id`,`scheme_name`, `due_method`, `profit_method`, `interest_rate_percent`, `due_period_percent`, `doc_charge_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_type`, `processing_fee_min`, `processing_fee_max`,`overdue_penalty_percent` FROM `scheme` WHERE FIND_IN_SET(id, '$scheme_id') ");
if ($qry->rowCount() > 0) {
    while ($scheme_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        if($scheme_info['due_method'] =='1'){
            $scheme_info['due_method'] = 'Monthly';

        }else if($scheme_info['due_method'] =='2'){
            $scheme_info['due_method'] = 'Weekly';   

        }else if($scheme_info['due_method'] =='3'){
            $scheme_info['due_method'] = 'Daily';
            
        }
        $scheme_info['doc_charge_min'] = ($scheme_info['doc_charge_type'] == 'percent') ? $scheme_info['doc_charge_min'].'% ' : $scheme_info['doc_charge_min'].'₹ ';
        $scheme_info['doc_charge_max'] = ($scheme_info['doc_charge_type'] == 'percent') ? $scheme_info['doc_charge_max'].'% ' : $scheme_info['doc_charge_max'].'₹ ';
        $scheme_info['processing_fee_min'] = ($scheme_info['processing_fee_type'] == 'percent') ? $scheme_info['processing_fee_min'].'% ' : $scheme_info['processing_fee_min'].'₹ ';
        $scheme_info['processing_fee_max'] = ($scheme_info['processing_fee_type'] == 'percent') ? $scheme_info['processing_fee_max'].'% ' : $scheme_info['processing_fee_max'].'₹ ';
        $scheme_arr[] = $scheme_info;
    }
}

$pdo = null; //Connection Close.

echo json_encode($scheme_arr);
