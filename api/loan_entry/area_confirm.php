<?php
require '../../ajaxconfig.php';
@session_start();
$cus_id = $_POST['cus_id'];
$area_confirm = $_POST['area_confirm'];

$response = array();
if ($area_confirm == '1') {
    $stmt = $pdo->prepare("SELECT `res_type`, `res_detail`, `res_address`, `native_address` FROM `customer_profile` WHERE `cus_id` = ?");
    $stmt->execute([$cus_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was found
    if ($row) {
        $response = $row;
    } else {
        $response['error'] = 'No resident info found for this customer.';
    }
} elseif ($area_confirm == '2') {
    // Fetch occupation info from the database
    $stmt = $pdo->prepare("SELECT `occupation`, `occ_detail`, `occ_income`, `occ_address` FROM `customer_profile` WHERE `cus_id` = ?");
    $stmt->execute([$cus_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data was found
    if ($row) {
        $response = $row;
    } else {
        $response['error'] = 'No occupation info found for this customer.';
    }
} else {
    $response['error'] = 'Invalid area_confirm option.';
}

// Return JSON response
echo json_encode($response);
?>
