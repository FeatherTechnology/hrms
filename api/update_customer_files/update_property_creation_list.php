<?php
require '../../ajaxconfig.php';

$property_list_arr = array();
$cus_id = $_POST['cus_id'];
$i = 0;
$qry = $pdo->query("SELECT pi.id, pi.property, pi.property_detail,CASE 
            WHEN pi.property_holder = 0 THEN cp.cus_name 
            ELSE fi.fam_name 
        END as property_holder, fi.fam_relationship 
FROM property_info pi 
LEFT JOIN family_info fi ON pi.property_holder = fi.id LEFT JOIN customer_profile cp ON pi.cus_profile_id= cp.id WHERE pi.cus_id = '$cus_id' GROUP BY pi.id");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        if ($row['fam_relationship'] == null) {
            $row['fam_relationship'] = 'Customer'; // Assign 'Customer' if null
        } else {
            $row['fam_relationship'] = $row['fam_relationship']; // Keep original value if not null
        }
        $row['action'] = "<span class='icon-border_color propertyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete propertyDeleteBtn' value='" . $row['id'] . "'></span>";

        $property_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($property_list_arr);
$pdo = null; // Close Connection




