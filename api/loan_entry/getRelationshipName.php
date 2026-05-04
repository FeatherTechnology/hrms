<?php
require '../../ajaxconfig.php';

$response = array();

if (isset($_POST['property_holder_id'])) {
    $property_holder_id = $_POST['property_holder_id'];

    $stmt = $pdo->prepare("SELECT fam_relationship FROM family_info WHERE id = ?");
    $stmt->execute([$property_holder_id]);

    // Fetch the result
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['prop_relationship'] = $row['fam_relationship'];
    } else {
        $response['prop_relationship'] = ''; // No relationship found
    }
} else {
    $response['prop_relationship'] = ''; // No property holder ID provided
}

$pdo = null; // Close the connection

echo json_encode($response);
?>
