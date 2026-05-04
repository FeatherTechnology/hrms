<?php
require '../../ajaxconfig.php';
$cp_id = $_POST['cp_id'];

$qry = $pdo->query("SELECT cp.aadhar_num, cp.cus_name, lelc.cus_id, lelc.loan_id, lc.loan_category, lelc.profit_type, lelc.scheme_due_method, lelc.due_type
    FROM loan_entry_loan_calculation lelc
    LEFT JOIN customer_profile cp ON lelc.cus_profile_id = cp.id
    LEFT JOIN loan_category lc ON lelc.loan_category = lc.id
    WHERE lelc.cus_profile_id = '$cp_id'
");

$row = $qry->fetch(PDO::FETCH_ASSOC);

$profit_type        = (int)$row['profit_type'];
$scheme_due_method  = (int)$row['scheme_due_method'];
$due_type           = $row['due_type'];

// BASE RESPONSE
$response = [
    'aadhar_num'    => $row['aadhar_num'],
    'cus_id'        => $row['cus_id'],
    'cus_name'      => $row['cus_name'],
    'loan_id'       => $row['loan_id'],
    'loan_category' => $row['loan_category'],
];

// ADD LOGIC
if ($profit_type === 0) {
    $response['due_method'] = 'Monthly';
    $response['loan_type']  = $due_type;
} else {
    $response['due_method'] = (
        $scheme_due_method === 1 ? 'Monthly' :
        ($scheme_due_method === 2 ? 'Weekly' :
        ($scheme_due_method === 3 ? 'Daily' : ''))
    );
    $response['loan_type'] = 'Scheme';
}

echo json_encode($response);
