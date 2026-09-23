<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'] ?? '';

if (empty($company_id)) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT 
        dc.id,
        dc.designation
    FROM company_designation_mapping cdm
    INNER JOIN designation_creation dc 
        ON dc.id = cdm.designation_id
    WHERE cdm.company_id = ?
      AND dc.designation_status = 0
      AND dc.designation_level < (
          SELECT MAX(dc2.designation_level)
          FROM company_designation_mapping cdm2
          INNER JOIN designation_creation dc2
              ON dc2.id = cdm2.designation_id
          WHERE cdm2.company_id = ?
            AND dc2.designation_status = 0
      )
    ORDER BY dc.designation_level ASC
");

$stmt->execute([$company_id, $company_id]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
