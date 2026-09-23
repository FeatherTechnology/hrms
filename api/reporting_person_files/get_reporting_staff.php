<?php

require '../../ajaxconfig.php';

$person_id           = $_POST['person_id'] ?? '';
$type                = $_POST['type'] ?? '';
$company_id          = $_POST['company_id'] ?? '';
$reporting_person_id = $_POST['reporting_person_id'] ?? '';

/*
 * reporting_person_id:
 *
 * 0 = New mapping
 * >0 = Existing reporting_person record being edited
 */

$reporting_person_id = (int)$reporting_person_id;

if (empty($person_id) || empty($type) || empty($company_id)) {

    echo json_encode([]);

    exit;
}

try {

    /*
     * ==========================================================
     * DIRECTOR
     * ==========================================================
     *
     * For Director:
     *
     * Get all Staff who are already configured as
     * Reporting Persons with user_type = 2
     * under this company.
     *
     * Example:
     *
     * Director
     *     |
     *     +---- Manager
     *     +---- Team Leader
     *
     */

    if ($type === 'director') {

        $stmt = $pdo->prepare("SELECT

            sc.id AS id,
            sc.staff_name,

            bc.branch_name,
            dc.department_name,
            tn.team_name,
            dsg.designation AS designation_name,

            CASE

                WHEN EXISTS (

                    SELECT 1

                    FROM reporting_person_mapping rpm

                    INNER JOIN reporting_person rp2
                        ON rp2.id = rpm.reporting_person_id

                    WHERE rpm.reporting_staff = sc.id

                    AND rp2.user_type = 1

                    AND rp2.company_id = ?

                    AND rpm.reporting_person_id != ?

                )

                THEN 1
                ELSE 0

            END AS is_mapped


        FROM reporting_person rp


        /*
         * Only Reporting Persons with user_type = 2
         *
         * These are the staff who can be selected
         * under a Director.
         */

        INNER JOIN staff_creation sc
            ON sc.id = rp.reporting_person


        /*
         * Current occupation
         */

        INNER JOIN occupation_info oi

            ON oi.id = (

                SELECT MAX(oi2.id)

                FROM occupation_info oi2

                WHERE oi2.staff_profile_id = sc.id

                AND (
                        oi2.effective_from IS NULL
                        OR oi2.effective_from <= NOW()
                )

            )


        LEFT JOIN branch_creation bc
            ON bc.id = oi.branch_id


        LEFT JOIN department_creation dc
            ON dc.id = oi.department


        LEFT JOIN team_name_creation tn
            ON tn.id = oi.team


        LEFT JOIN designation_creation dsg
            ON dsg.id = oi.designation


        WHERE rp.user_type = 2

        AND rp.company_id = ?

        AND sc.status = 1


        ORDER BY sc.staff_name ASC

    ");

        $stmt->execute([

            $company_id,

            $reporting_person_id,

            $company_id

        ]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($type === 'staff') {


        /*
         * ======================================================
         * GET CURRENT DESIGNATION
         * ======================================================
         */

        $stmt = $pdo->prepare("SELECT

                oi.designation

            FROM staff_creation sc

            INNER JOIN occupation_info oi

                ON oi.id = (

                    SELECT MAX(oi2.id)

                    FROM occupation_info oi2

                    WHERE oi2.staff_profile_id = sc.id

                    AND (

                            oi2.effective_from IS NULL

                            OR oi2.effective_from <= NOW()

                    )

                )

            WHERE sc.id = ?

            AND sc.company_id = ?

            AND sc.status = 1

            LIMIT 1

        ");


        $stmt->execute([

            $person_id,

            $company_id

        ]);


        $designation = $stmt->fetchColumn();


        /*
         * Reporting Person not found.
         */

        if ($designation === false) {

            echo json_encode([]);

            exit;
        }


        /*
         * ======================================================
         * GET REPORTING STAFF
         * ======================================================
         *
         * designation > current designation
         *
         * Example:
         *
         * Current Staff = Team Leader (4)
         *
         * Available Reporting Staff:
         *
         * Manager (5)
         * Senior Manager (6)
         * etc.
         *
         */

        $stmt = $pdo->prepare("SELECT

        sc.id AS id,
        sc.staff_name,
        bc.branch_name,
        dc.department_name,
        tn.team_name,
        dsg.designation AS designation_name,

        CASE

            WHEN EXISTS (

                SELECT 1

                FROM reporting_person_mapping rpm2

                WHERE rpm2.reporting_staff = sc.id

                AND rpm2.reporting_person_id != ?

            )

            THEN 1

            ELSE 0

        END AS is_mapped

    FROM staff_creation sc

    INNER JOIN occupation_info oi
        ON oi.id = (

            SELECT MAX(oi2.id)

            FROM occupation_info oi2

            WHERE oi2.staff_profile_id = sc.id

            AND (
                    oi2.effective_from IS NULL
                    OR oi2.effective_from <= NOW()
            )

        )

    LEFT JOIN branch_creation bc
        ON bc.id = oi.branch_id

    LEFT JOIN department_creation dc
        ON dc.id = oi.department

    LEFT JOIN team_name_creation tn
        ON tn.id = oi.team

    LEFT JOIN designation_creation dsg
        ON dsg.id = oi.designation

    WHERE sc.company_id = ?

    AND sc.status = 1

    AND oi.designation > ?

    ORDER BY sc.staff_name ASC

");


        $stmt->execute([

            $reporting_person_id,

            $company_id,

            $designation

        ]);


        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {

        echo json_encode([]);

        exit;
    }


    /*
     * ==========================================================
     * CREATE LABEL
     * ==========================================================
     */

    foreach ($result as &$row) {

        $branch_name =
            $row['branch_name'] ?? '';

        $department_name =
            $row['department_name'] ?? '';

        $team_name =
            $row['team_name'] ?? '';

        $designation_name =
            $row['designation_name'] ?? '';


        $row['label'] =
            $row['staff_name'] .
            ' - (' .
            $branch_name .
            ' - ' .
            $department_name .
            ' - ' .
            $team_name .
            ' - ' .
            $designation_name .
            ')';


        $row['is_mapped'] =
            (int)($row['is_mapped'] ?? 0);
    }

    unset($row);


    /*
     * ==========================================================
     * RETURN JSON
     * ==========================================================
     */

    echo json_encode($result);
} catch (Exception $e) {

    echo json_encode([

        'status' => false,

        'message' => $e->getMessage()

    ]);
}
