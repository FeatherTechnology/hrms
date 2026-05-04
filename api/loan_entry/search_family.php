<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();
$family_response = array();
$customer_response = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$aadhar = isset($_POST['aadhar']) ? $_POST['aadhar'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT fi.*, le.cus_name AS under_customer_name, le.cus_id AS under_customer_id 
          FROM family_info fi
          JOIN customer_profile le ON fi.cus_id = le.cus_id 
          WHERE 1=1"; // Base query

if (!empty($name)) {
    $query .= " AND fi.fam_name LIKE '%$name%'";
}

if (!empty($aadhar)) {
    $query .= " AND fi.fam_aadhar = '$aadhar'";
}

if (!empty($mobile)) {
    $query .= " AND fi.fam_mobile = '$mobile'";
}

// Execute the query
$result = $pdo->query($query);

if ($result) {
    // Fetch all matching records
    $family_response = $result->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch the related customer details
    foreach ($family_response as $family_member) {
        $cus_id = $family_member['cus_id'];
        $customer_query = "SELECT * FROM customer_profile WHERE cus_id = '$cus_id'";
        $customer_result = $pdo->query($customer_query);
        if ($customer_result) {
            $customer_details = $customer_result->fetch(PDO::FETCH_ASSOC);
            if ($customer_details) {
                $customer_response[] = $customer_details;
            }
        }
    }
}

// Combine family and customer responses
$response['family'] = $family_response;
$response['customers'] = $customer_response;

// Return the response as JSON
echo json_encode($response);
?>
