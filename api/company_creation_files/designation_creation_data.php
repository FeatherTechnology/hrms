<?php

/** Fetch Designation Details **
 *
 * Purpose:
 * - Retrieves designation information based on the provided designation ID.
 * - Returns designation details in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT *
    FROM designation_creation
    WHERE id = ?
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
