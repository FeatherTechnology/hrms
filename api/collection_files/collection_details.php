<?php
require  '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$cp_id = $_POST['cp_id'];
$records = array();
$qry = $pdo->query("SELECT lelc.loan_category
FROM loan_entry_loan_calculation lelc
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
WHERE lelc.cus_profile_id = '$cp_id' AND cs.status = 7 ");
if ($qry->rowCount() > 0) {
    $row = $qry->fetch();
    $records['loan_category'] = $row["loan_category"];
}
$qry1 = $pdo->query("SELECT collection_access FROM users WHERE id = '$user_id' ");
if ($qry1->rowCount() > 0) {
    $row1 = $qry1->fetch();
    $records['collection_access'] = $row1["collection_access"]; //1=YES, 2 =NO//
}
echo json_encode($records);
