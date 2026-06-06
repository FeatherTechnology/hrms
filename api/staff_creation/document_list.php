<?php
require '../../ajaxconfig.php';

$staff_profile_id = $_POST['staff_profile_id'];
$doc_list_arr = array();
$doc_tye_arr = array("1" => "Original", "2" => "Copy");
$i = 0;
$qry = $pdo->query("SELECT di.id, 
                               di.doc_name, 
                               di.doc_type, 
                               di.upload,
                                 DATE_FORMAT(di.created_on, '%d-%m-%Y') as created_date,
                                 DATE_FORMAT(di.return_date, '%d-%m-%Y') as return_date
                        FROM document_info di 
                        WHERE di.staff_profile_id = '$staff_profile_id'");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $doc_list_arr[$i]['id'] = $row['id'];
        $doc_list_arr[$i]['doc_name'] = $row['doc_name'];
        $doc_list_arr[$i]['doc_type'] = $doc_tye_arr[$row['doc_type']];
        $doc_list_arr[$i]['created_date'] = $row['created_date'];
        $doc_list_arr[$i]['return_date'] = $row['return_date'];
        $doc_list_arr[$i]['upload'] = "<a href='uploads/staff_creation/document/" . $row['upload'] . "' target='_blank'>" . $row['upload'] . "</a>";

        // Construct action buttons
        $action_buttons = "<span class='icon-border_color documentActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete documentDeleteBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $doc_list_arr[$i]['action'] = $action_buttons;
        if ($row['return_date'] == '') {
            $doc_list_arr[$i]['info'] = "<button class='btn btn-primary returnBtn' value='" . $row['id'] . "'>Return</button>";
        } else {
            $doc_list_arr[$i]['info'] = "<span style='color:green;font-weight:bold;'>Returned</span>";
        }

        $i++;
    }
}

echo json_encode($doc_list_arr);

$pdo = null; // Close Connection
