<?php
require '../../ajaxconfig.php';

$cus_profile_id = $_POST['cus_profile_id'];;
$qry = $pdo->query("
    SELECT si.balance_amount
    FROM loan_issue si
    WHERE si.cus_profile_id = '$cus_profile_id' 
    ORDER BY si.id DESC
    LIMIT 1
");
if ($qry->rowCount() > 0) {
    $result = $qry->fetch(PDO::FETCH_ASSOC);
} else {
    $result = ['balance_amount' => null]; // Default to 0 if no records found
}

$pdo = null; // Close connection
echo json_encode($result);
?>
