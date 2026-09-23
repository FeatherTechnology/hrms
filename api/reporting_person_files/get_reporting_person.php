<?php

require '../../ajaxconfig.php';


$designation = $_POST['designation'] ?? '';

$reporting_person_id =
    $_POST['reporting_person_id'] ?? '';


if (empty($designation)) {

    echo json_encode([]);

    exit;
}


try {

    $stmt = $pdo->prepare("SELECT

            sc.id,
            sc.staff_name,

            /*
             * Check whether this staff member
             * is already used as a Reporting Person
             * in another mapping.
             */

            CASE

                WHEN EXISTS (

                    SELECT 1

                    FROM reporting_person rp2

                    WHERE rp2.reporting_person = sc.id

                    AND rp2.id != ?

                )

                THEN 1

                ELSE 0

            END AS is_mapped


        FROM staff_creation sc


        /*
         * ======================================================
         * GET CURRENT EFFECTIVE OCCUPATION
         * ======================================================
         *
         * Do NOT simply use MAX(id).
         *
         * Only occupation records whose effective_from
         * is today or earlier are considered.
         *
         * Example:
         *
         * 2026-07-27  -> active
         * 2026-09-10  -> active
         * 2026-10-01  -> future, ignored
         *
         * Therefore the latest active record is selected.
         */

        LEFT JOIN occupation_info oi

            ON oi.id = (

                SELECT MAX(oi2.id)

                FROM occupation_info oi2

                WHERE oi2.staff_profile_id = sc.id

                  AND (

                      oi2.effective_from IS NULL

                      OR oi2.effective_from <= NOW()

                  )

            )


        /*
         * ======================================================
         * CONDITIONS
         * ======================================================
         */

        WHERE oi.designation = ?

          AND sc.status = 1


        ORDER BY sc.staff_name ASC

    ");


    $stmt->execute([

        $reporting_person_id,

        $designation

    ]);


    $result =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
     * ==========================================================
     * CONVERT is_mapped TO INTEGER
     * ==========================================================
     */

    foreach ($result as &$row) {

        $row['is_mapped'] =
            (int)$row['is_mapped'];
    }

    unset($row);


    /*
     * ==========================================================
     * RETURN
     * ==========================================================
     */

    echo json_encode($result);
} catch (Exception $e) {

    echo json_encode([

        'status' => false,

        'message' => $e->getMessage()

    ]);
}
