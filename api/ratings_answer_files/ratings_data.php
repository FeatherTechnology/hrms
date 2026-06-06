<?php

/** Fetch Rating Title **
 * Purpose:
 * - Retrieves the rating title based on the provided rating ID.
 * - Returns rating title data in JSON format.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT rating_title FROM rating_titles WHERE id = ?");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
