<?php
include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';

$result = [];

if (!empty($user_id)) {

    $stmt = $pdo->prepare("SELECT allowed_request_type FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user['allowed_request_type'])) {

        $requestTypes = [
            4 => 'OT',
            1 => 'Leave',
            3 => 'Week Off',
            2 => 'Permission',

            
        ];

         $ids = array_map('trim', explode(',', $user['allowed_request_type']));

        foreach ($requestTypes as $id => $name) {
            if (in_array((string)$id, $ids, true)) {
                $result[] = [
                    'id' => (string)$id,
                    'request_type' => $name
                ];
            }
        }
    }
}

echo json_encode($result);
$pdo = null;
