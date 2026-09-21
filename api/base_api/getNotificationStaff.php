<?php

session_start();
require "../../ajaxconfig.php";

$user_id = $_SESSION['user_id'];

// Get logged-in user's allowed approval types
$stmt = $pdo->prepare("SELECT approved_request_type
    FROM users
    WHERE id = ?
");
$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['approved_request_type'])) {
    echo json_encode([]);
    exit;
}

$approvedTypes = array_filter(array_map('trim', explode(',', $user['approved_request_type'])));

$placeholders = implode(',', array_fill(0, count($approvedTypes), '?'));

$sql = "
    SELECT
        r.req_type,
        sc.staff_id,
        sc.staff_name
    FROM regularization r
    INNER JOIN staff_creation sc
        ON sc.id = r.staff_profile_id
    WHERE r.status = 0
      AND r.req_type IN ($placeholders)
    ORDER BY r.req_type, sc.staff_name
";

$stmt = $pdo->prepare($sql);
$stmt->execute($approvedTypes);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
