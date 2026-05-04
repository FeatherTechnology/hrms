<?php
require '../../ajaxconfig.php';
$response = [];

$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : null;
$customer_area_id = null;

// 1. Get customer's saved area
if ($cus_profile_id) {
    $cusQry = $pdo->query("SELECT area FROM customer_profile WHERE id = '$cus_profile_id' ");
    $cusRow = $cusQry->fetch(PDO::FETCH_ASSOC);
    $customer_area_id = $cusRow ? $cusRow['area'] : null;
}

$response1 = [];
$response2 = [];

// 2. Get customer's saved area details (even if inactive/unmapped)
if ($customer_area_id) {
    $qry = $pdo->query("
        SELECT anc.id, anc.areaname, anc.status
        FROM area_name_creation anc
        WHERE anc.id = '$customer_area_id'
        GROUP BY anc.id
    ");
    if ($qry->rowCount() > 0) {
        $response1 = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
}

// 3. Get all mapped active areas
$qry1 = $pdo->query("
    SELECT anc.id, anc.areaname, anc.status
    FROM area_creation ac
    LEFT JOIN area_creation_area_name acan ON ac.id = acan.area_creation_id
    LEFT JOIN area_name_creation anc ON acan.area_id = anc.id
    WHERE ac.status = 1 AND anc.status = 1
    GROUP BY anc.id
");
if ($qry1->rowCount() > 0) {
    $response2 = $qry1->fetchAll(PDO::FETCH_ASSOC);
}

// 4. Merge both responses without duplicates
$merged = array_merge($response1, $response2);
$unique = [];
foreach ($merged as $row) {
    $unique[$row['id']] = $row; // Use area_id as key to remove duplicates
}
$response = array_values($unique);

$pdo = null;
echo json_encode($response);


