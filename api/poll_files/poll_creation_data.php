<?php

/** Fetch Poll Details **
 * Purpose:
 * - Retrieves poll information based on the provided poll ID.
 * - Fetches mapped department IDs.
 * - Fetches poll option values.
 * - Converts department IDs and poll options into arrays.
 * - Returns poll details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        pt.*,
        GROUP_CONCAT(DISTINCT pdm.department_id) AS department_ids,
        GROUP_CONCAT(
            DISTINCT pom.poll_options
            SEPARATOR '||'
        ) AS poll_options
    FROM poll_titles pt
    LEFT JOIN poll_department_mapping pdm
        ON pt.id = pdm.poll_titles_id
    LEFT JOIN poll_options_mapping pom
        ON pt.id = pom.poll_titles_id
    WHERE pt.id = ?
    GROUP BY pt.id
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $result['department_ids'] = !empty($result['department_ids'])
        ? explode(',', $result['department_ids'])
        : [];

    $result['poll_options'] = !empty($result['poll_options'])
        ? explode('||', $result['poll_options'])
        : [];
}

$pdo = null; // Close Connection

echo json_encode($result);
