<?php
require '../../ajaxconfig.php';

$mobile = $_POST['mobile'];

$stmt = $pdo->prepare('SELECT cp.mobile1, cp.mobile2, cs.status FROM customer_profile cp JOIN customer_status cs ON cs.id = cp.id WHERE cp.mobile1 = ? OR cp.mobile2 = ?');
$stmt->execute([$mobile, $mobile]);
$response = $stmt->fetch();

if ($response) {
    echo json_encode(['exists' => true, 'status' => $response['status']]);
} else {
    echo json_encode(['exists' => false]);
}

// Close the connection
$pdo = null;
?>

