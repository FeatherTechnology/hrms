<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'] ?? '';

if (empty($company_id)) {

    echo json_encode([]);

    exit;
}

try {

    /*
     * ==========================================================
     * GET REPORTING PERSONS
     * ==========================================================
     *
     * For Director:
     *
     * reporting_person table
     * user_type = 2
     *
     * reporting_person = staff_creation.id
     *
     */

    $stmt = $pdo->prepare("

        SELECT

            rp.id AS reporting_mapping_id,

            rp.reporting_person,

            sc.staff_name,

            CASE

                WHEN EXISTS (

                    SELECT 1

                    FROM reporting_person rp2

                    WHERE rp2.reporting_person = rp.reporting_person

                      AND rp2.id != rp.id

                )

                THEN 1

                ELSE 0

            END AS is_mapped

        FROM reporting_person rp

        INNER JOIN staff_creation sc
            ON sc.id = rp.reporting_person

        WHERE rp.company_id = ?

          AND rp.user_type = 2

          AND sc.status = 1

        ORDER BY sc.staff_name ASC

    ");

    $stmt->execute([
        $company_id
    ]);

    $result =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($result as &$row) {

        $row['is_mapped'] =
            (int)$row['is_mapped'];
    }

    unset($row);


    echo json_encode($result);
} catch (Exception $e) {

    echo json_encode([

        'status' => false,

        'message' => $e->getMessage()

    ]);
}
