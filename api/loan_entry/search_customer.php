<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();
$familyResponse = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$aadhar_num = isset($_POST['aadhar_num']) ? $_POST['aadhar_num'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT DISTINCT cus_id, cus_name, mobile1, mobile2, aadhar_num FROM customer_profile WHERE 1";

if (!empty($name)) {
    $query .= " AND cus_name = '$name'";
}

if (!empty($aadhar_num)) {
    $query .= " AND aadhar_num = '$aadhar_num'";
}

if (!empty($mobile)) {
    $query .= " AND (mobile1 = '$mobile' OR mobile2 = '$mobile')";
}

// Execute the query
$result = $pdo->query($query);
if ($result) {
    // Fetch all matching records
    $response = $result->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch related family info
$familyQuery = "SELECT DISTINCT fi.*, cp.cus_name AS under_customer_name, cp.cus_id AS under_customer_id 
                FROM family_info fi 
                LEFT JOIN customer_profile cp ON fi.cus_id = cp.cus_id 
                WHERE 1";

if (!empty($name)) {
    $familyQuery .= " AND fi.fam_name = '$name'";
}

if (!empty($aadhar_num)) {
    $familyQuery .= " AND fi.fam_aadhar = '$aadhar_num'";
}

if (!empty($mobile)) {
    $familyQuery .= " AND fi.fam_mobile = '$mobile'";
}

$familyResult = $pdo->query($familyQuery);
if ($familyResult) {
    $familyResponse = $familyResult->fetchAll(PDO::FETCH_ASSOC);
}

// Return the response as JSON
echo json_encode(array(
    'customers' => $response,
    'family' => $familyResponse
));
