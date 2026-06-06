<?php

/** User Screen Permissions **
 * Purpose:
 * - Fetches all screens assigned to the selected user.
 * - Retrieves main menu and sub menu links.
 * - Returns screen permission details in JSON format.
 */

require '../../ajaxconfig.php';

$user_id = $_POST['user_id'];

$response = [];

$stmt = $pdo->prepare("SELECT
        m.link AS main_menu_link,
        s.id AS sub_menu_id,
        s.link AS sub_menu_link
    FROM users u
    LEFT JOIN sub_menu_list s ON FIND_IN_SET(s.id, u.screens)
    LEFT JOIN menu_list m ON m.id = s.main_menu
    WHERE u.id = ?
");

$stmt->execute([$user_id]);

if ($stmt->rowCount() > 0) {
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($response);
