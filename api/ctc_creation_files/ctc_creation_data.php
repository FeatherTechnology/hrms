<?php

/** Fetch CTC Component Details **
 * Purpose:
 * - Retrieves CTC component information based on the provided CTC ID.
 * - Returns CTC component details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT *
    FROM ctc_creation
    WHERE id = ?
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
