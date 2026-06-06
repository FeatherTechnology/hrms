<?php

/** Menu and Sub Menu List **
 * Purpose:
 * - Fetches all menu and sub menu records.
 * - Retrieves menu names, links, and sub menu details.
 * - Excludes the specified sub menu ID.
 * - Returns menu data in JSON format.
 */

require "../../ajaxconfig.php";

$response = [];

$stmt = $pdo->prepare("SELECT
        m.menu AS main_menu,
        m.link AS main_menu_link,
        s.id AS sub_menu_id,
        s.sub_menu,
        s.link AS sub_menu_link
    FROM menu_list m
    LEFT JOIN sub_menu_list s ON m.id = s.main_menu
    WHERE s.id != ?
");

$stmt->execute([1]);

if ($stmt->rowCount() > 0) {
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($response);
