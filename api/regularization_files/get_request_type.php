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
            1 => 'Leave',
            2 => 'Permission',
            3 => 'Week Off',
            4 => 'OT'
        ];

        $ids = explode(',', $user['allowed_request_type']);

        foreach ($ids as $id) {
            $id = trim($id);

            if (isset($requestTypes[$id])) {
                $result[] = [
                    'id' => $id,
                    'request_type' => $requestTypes[$id]
                ];
            }
        }
    }
}

echo json_encode($result);
$pdo = null;
