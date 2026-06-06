<?php

/** Fetch Rating Details **
 * Purpose:
 * - Retrieves rating information based on the provided rating ID.
 * - Fetches mapped department IDs.
 * - Converts department IDs into an array.
 * - Returns rating details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        rt.*,
        GROUP_CONCAT(DISTINCT rdm.department_id) AS department_ids
    FROM rating_titles rt
    LEFT JOIN rating_department_mapping rdm
        ON rt.id = rdm.rating_titles_id
    WHERE rt.id = ?
    GROUP BY rt.id
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $result['department_ids'] = !empty($result['department_ids'])
        ? explode(',', $result['department_ids'])
        : [];
}

$pdo = null; // Close Connection

echo json_encode($result);
