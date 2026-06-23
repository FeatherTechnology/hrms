<?php

/** Designation Dropdown List **
 * Purpose:
 * - Fetches all active designations.
 * - Returns designation data for dropdown selection.
 * - Returns data in JSON format.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'] ?? '';

$sql = "
    SELECT DISTINCT dc.id, dc.designation
    FROM designation_creation dc
    LEFT JOIN company_designation_mapping cdm
        ON dc.id = cdm.designation_id
    WHERE dc.designation_status = 0
    AND (
        (? = '' AND cdm.designation_id IS NULL)
        OR
        (? != '' AND (
            cdm.company_id = ?
            OR cdm.designation_id IS NULL
        ))
    )
    ORDER BY dc.designation ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id, $company_id, $company_id]);

$result = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $result[] = [
        'id' => $row['id'],
        'designation' => $row['designation']
    ];
}

echo json_encode($result);

$pdo = null;
?>