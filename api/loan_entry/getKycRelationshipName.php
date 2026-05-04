<?php
require '../../ajaxconfig.php';

$response = array();

if (isset($_POST['family_member_id'])) {
    $family_member_id = $_POST['family_member_id'];

    // Prepare and execute the query to fetch the relationship based on the property holder ID
    $stmt = $pdo->prepare("SELECT fam_relationship FROM family_info WHERE id = ?");
    $stmt->execute([$family_member_id]);

    // Fetch the result
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['kyc_relationship'] = $row['fam_relationship'];
    } else {
        $response['kyc_relationship'] = ''; // No relationship found
    }
} else {
    $response['kyc_relationship'] = ''; // No property holder ID provided
}

$pdo = null; // Close the connection

echo json_encode($response);
?>
