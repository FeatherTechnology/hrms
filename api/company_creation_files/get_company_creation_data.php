<?php

/** Fetch Company Details **
 * Purpose:
 * - Retrieves company information based on the provided company ID.
 * - Fetches mapped department IDs and designation IDs.
 * - Returns company details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        cc.*,
        GROUP_CONCAT(DISTINCT cdm.department_id) AS department_ids,
        GROUP_CONCAT(DISTINCT dm.designation_id) AS designation_ids
    FROM company_creation cc
    LEFT JOIN company_department_mapping cdm
        ON cc.id = cdm.company_id
    LEFT JOIN company_designation_mapping dm
        ON cc.id = dm.company_id
    WHERE cc.id = ?
    GROUP BY cc.id
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close Connection

echo json_encode($result);
