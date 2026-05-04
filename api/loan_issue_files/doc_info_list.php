<?php
require "../../ajaxconfig.php";

$doc_info_arr = array();
$cus_profile_id = $_POST['cus_profile_id'];

// Corrected SQL query
$qry = $pdo->query("
    SELECT 
        di.id as d_id, 
        di.*, 
        CASE 
            WHEN di.holder_name = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as holder_name, 
        fi.* 
    FROM document_info di 
    LEFT JOIN family_info fi ON di.holder_name = fi.id 
    LEFT JOIN customer_profile cp ON di.cus_profile_id= cp.id 
    WHERE di.cus_profile_id = '$cus_profile_id'
");

if ($qry->rowCount() > 0) {
    while ($doc_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $doc_info['doc_type'] = ($doc_info['doc_type'] == '1') ? 'Original' : 'Xerox';
        $doc_info['upload'] = "<a href='uploads/loan_issue/doc_info/".$doc_info['upload']."' target='_blank'>".$doc_info['upload']."</a>";
        $doc_info['action'] = "<span class='icon-border_color docActionBtn' value='" . $doc_info['d_id'] . "'></span> <span class='icon-trash-2 docDeleteBtn' value='" . $doc_info['d_id'] . "'></span>";
        $doc_info_arr[] = $doc_info;
    }
}

$pdo = null; // Connection Close.
echo json_encode($doc_info_arr);
