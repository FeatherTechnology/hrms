<?php
require '../../ajaxconfig.php';

$cus_id = $_POST['cus_id'];
$kyc_list_arr = array();
$i = 0;

try {
    $qry = $pdo->query("SELECT ki.id, 
                               lelc.loan_id,
                               ki.proof_of, 

                               CASE 
                                   WHEN ki.proof_of = 1 THEN cp.cus_name 
                                   ELSE fi.fam_name 
                               END AS proof_of_name, 
                               CASE 
                                   WHEN ki.proof_of = 1 THEN 'NIL' 
                                   ELSE fi.fam_relationship 
                               END AS fam_relationship, 
                               pi.addProof_name as proof, 
                               ki.proof_detail, 
                               ki.upload 
                        FROM kyc_info ki
                        JOIN proof_info pi ON ki.proof = pi.id
                        LEFT JOIN loan_entry_loan_calculation lelc ON ki.cus_profile_id = lelc.cus_profile_id
                        LEFT JOIN family_info fi ON ki.fam_mem = fi.id 
                         LEFT JOIN customer_profile cp ON ki.cus_id =cp.cus_id WHERE ki.cus_id = '$cus_id'GROUP BY ki.id");

    if (!$qry) {
        throw new Exception("Database query failed: " . implode(" - ", $pdo->errorInfo()));
    }

    if ($qry->rowCount() > 0) {
        while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
            $kyc_list_arr[$i]['id'] = $row['id'];
            $kyc_list_arr[$i]['loan_id'] = $row['loan_id'];
            $kyc_list_arr[$i]['proof_of'] = $row['proof_of_name'];
            $kyc_list_arr[$i]['fam_relationship'] = $row['fam_relationship'];
            $kyc_list_arr[$i]['proof'] = $row['proof'];
            $kyc_list_arr[$i]['proof_detail'] = $row['proof_detail'];
            $kyc_list_arr[$i]['upload'] = "<a href='uploads/loan_entry/kyc/" . $row['upload'] . "' target='_blank'>" . $row['upload'] . "</a>";

            // Construct action buttons
            $action_buttons = "<span class='icon-border_color kycActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
            $action_buttons .= "<span class='icon-delete kycDeleteBtn' value='" . $row['id'] . "'></span>";
            $kyc_list_arr[$i]['action'] = $action_buttons;

            $i++;
        }
    }

    echo json_encode($kyc_list_arr);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $pdo = null; // Close Connection
}
?>

