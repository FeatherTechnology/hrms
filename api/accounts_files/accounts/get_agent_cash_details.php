<?php
require "../../../ajaxconfig.php";

$id = $_POST['id'];
$cash_type = $_POST['coll_mode'];

// set condition for payment mode
if ($cash_type == '1') {
    $cndtn = "li.payment_mode = '1' ";
    $cndtn1 = "coll_mode = '1' ";
    $sumField = "SUM(li.cash) AS total_amount";
} else {
    $cndtn = "li.payment_mode != '1' ";
    $cndtn1 = "coll_mode = '2' "; // bank/cheque
    $sumField = "SUM(li.cheque_val + li.transaction_val) AS total_amount";
}

//payment_mode = 1 - cash; 2 - bank; 3 - cheque;

$row = array();
$qry = $pdo->query("
    SELECT 
        COUNT(li.id) AS total_issued,
        $sumField
    FROM loan_issue li 
    LEFT JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id 
    LEFT JOIN loan_entry_loan_calculation lc ON li.cus_profile_id = lc.cus_profile_id 
    WHERE lc.referred = '0' 
      AND lc.agent_id = '$id' 
      AND $cndtn 
      AND li.issue_date > COALESCE(
            (SELECT created_on FROM expenses 
             WHERE agent_id = '$id' AND $cndtn1 
             ORDER BY id DESC LIMIT 1), 
            '1970-01-01 00:00:00'
      ) 
      AND li.issue_date <= NOW()
");

if ($qry->rowCount() > 0) {
    $row = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; //Close Connection.
echo json_encode($row);
?>
