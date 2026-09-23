<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'] ?? '';

if (empty($company_id)) {
    echo json_encode([]);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT DISTINCT
            dc.id,
            dc.director_id,
            dc.director_name,

            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM reporting_person rp
                    WHERE rp.director_id = dc.id
                      AND rp.company_id = ?
                      AND rp.user_type = 1
                )
                THEN 1
                ELSE 0
            END AS is_mapped

        FROM users u

        INNER JOIN director_creation dc
            ON dc.id = u.director_name

        WHERE FIND_IN_SET(
            ?,
            REPLACE(u.director_company, ' ', '')
        )

        ORDER BY dc.director_name ASC
    ");

    $stmt->execute([
        $company_id,
        $company_id
    ]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as &$row) {
        $row['is_mapped'] = (int)$row['is_mapped'];
    }

    unset($row);

    echo json_encode($result);
} catch (Exception $e) {

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
