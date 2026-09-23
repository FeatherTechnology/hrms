<?php

require '../../ajaxconfig.php';

$reporting_person_id = $_POST['reporting_person_id'] ?? '';

/*
 * ==========================================================
 * VALIDATE REPORTING PERSON
 * ==========================================================
 */

if (empty($reporting_person_id)) {

    echo json_encode([
        'status' => false,
        'message' => 'Invalid reporting person'
    ]);

    exit;
}

try {

    /*
     * ==========================================================
     * GET ROOT REPORTING PERSON
     * ==========================================================
     */

    $stmt = $pdo->prepare("
        SELECT

            rp.id AS reporting_person_id,

            rp.user_type,

            rp.reporting_person,

            rp.director_id,

            rp.company_id,

            CASE
                WHEN rp.user_type = 1
                    THEN dir.director_name
                ELSE sc.staff_name
            END AS staff_name,

            CASE
                WHEN rp.user_type = 1
                    THEN 'Director'
                ELSE dsg.designation
            END AS designation_name

        FROM reporting_person rp

        /*
         * Staff
         */
        LEFT JOIN staff_creation sc
            ON sc.id = rp.reporting_person
           AND sc.company_id = rp.company_id

        /*
         * Director
         */
        LEFT JOIN director_creation dir
            ON dir.id = rp.director_id

        /*
         * Get current occupation
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
         * Designation
         */
        LEFT JOIN designation_creation dsg
            ON dsg.id = oi.designation

        WHERE rp.id = ?

        LIMIT 1
    ");

    $stmt->execute([
        $reporting_person_id
    ]);

    $root = $stmt->fetch(PDO::FETCH_ASSOC);


    /*
     * ==========================================================
     * ROOT NOT FOUND
     * ==========================================================
     */

    if (!$root) {

        echo json_encode([
            'status' => false,
            'message' => 'Reporting person not found'
        ]);

        exit;
    }


    /*
     * ==========================================================
     * RECURSIVE HIERARCHY FUNCTION
     * ==========================================================
     *
     * IMPORTANT:
     *
     * $visited is NOT passed by reference.
     *
     * Every branch receives its own copy.
     *
     * This allows the same reporting person to appear under
     * different branches without losing its children.
     *
     * At the same time, it prevents circular relationships.
     *
     * Example:
     *
     * Director
     *    |
     *    +---- Sundar
     *    |       |
     *    |       +---- Sunil
     *    |
     *    +---- Sunil
     *
     * Sunil can have children in BOTH branches.
     *
     * But:
     *
     * Sunil -> Ramesh -> Sunil
     *
     * will still be stopped.
     *
     * ==========================================================
     */

    function getHierarchyChildren(
        PDO $pdo,
        $reportingPersonId,
        $companyId,
        $visited = []
    ) {

        /*
         * ======================================================
         * PREVENT CIRCULAR REPORTING
         * ======================================================
         */

        if (isset($visited[$reportingPersonId])) {
            return [];
        }

        /*
         * Add current reporting person to THIS branch's
         * visited list.
         *
         * Since $visited is passed by value, other branches
         * will not be affected.
         */

        $visited[$reportingPersonId] = true;


        /*
         * ======================================================
         * GET DIRECT REPORTING STAFF
         * ======================================================
         */

        $stmt = $pdo->prepare("

            SELECT

                /*
                 * Mapping ID
                 */
                rpm.id AS mapping_id,

                /*
                 * Staff ID
                 */
                sc.id AS staff_id,

                /*
                 * Staff Name
                 */
                sc.staff_name,

                /*
                 * Current designation ID
                 */
                oi.designation AS designation_id,

                /*
                 * Current designation name
                 */
                dsg.designation AS designation_name,

                /*
                 * Branch
                 */
                bc.branch_name,

                /*
                 * Department
                 */
                dep.department_name,

                /*
                 * Team
                 */
                tn.team_name,

                /*
                 * Check whether this staff is also a
                 * reporting person.
                 *
                 * If yes, we can recursively get its children.
                 */
                rp.id AS child_reporting_person_id

            FROM reporting_person_mapping rpm


            /*
             * ==================================================
             * STAFF
             * ==================================================
             */

            INNER JOIN staff_creation sc
                ON sc.id = rpm.reporting_staff


            /*
             * ==================================================
             * CURRENT OCCUPATION
             * ==================================================
             *
             * Get the latest effective occupation record.
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


            /*
             * ==================================================
             * DESIGNATION
             * ==================================================
             */

            LEFT JOIN designation_creation dsg
                ON dsg.id = oi.designation


            /*
             * ==================================================
             * BRANCH
             * ==================================================
             */

            LEFT JOIN branch_creation bc
                ON bc.id = oi.branch_id


            /*
             * ==================================================
             * DEPARTMENT
             * ==================================================
             */

            LEFT JOIN department_creation dep
                ON dep.id = oi.department


            /*
             * ==================================================
             * TEAM
             * ==================================================
             */

            LEFT JOIN team_name_creation tn
                ON tn.id = oi.team


            /*
             * ==================================================
             * CHECK REPORTING PERSON
             * ==================================================
             *
             * If this staff is itself a reporting person,
             * retrieve its reporting_person.id.
             */

            LEFT JOIN reporting_person rp
                ON rp.reporting_person = sc.id

               AND rp.company_id = ?


            /*
             * ==================================================
             * CONDITIONS
             * ==================================================
             */

            WHERE rpm.reporting_person_id = ?

              AND sc.company_id = ?

              AND sc.status = 1


            /*
             * ==================================================
             * ORDER
             * ==================================================
             */

            ORDER BY sc.staff_name ASC

        ");


        /*
         * ======================================================
         * EXECUTE
         * ======================================================
         */

        $stmt->execute([

            $companyId,

            $reportingPersonId,

            $companyId

        ]);


        /*
         * ======================================================
         * FETCH
         * ======================================================
         */

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $children = [];


        /*
         * ======================================================
         * BUILD CHILDREN
         * ======================================================
         */

        foreach ($rows as $row) {

            /*
             * ==================================================
             * CREATE STAFF NODE
             * ==================================================
             */

            $child = [

                'staff_id' =>
                $row['staff_id'],

                'staff_name' =>
                $row['staff_name'],

                /*
                 * Always use current designation from
                 * occupation_info.
                 */
                'designation_name' =>
                $row['designation_name'] ?? '',

                'branch_name' =>
                $row['branch_name'] ?? '',

                'department_name' =>
                $row['department_name'] ?? '',

                'team_name' =>
                $row['team_name'] ?? '',

                'children' => []

            ];


            /*
             * ==================================================
             * RECURSIVE CHILDREN
             * ==================================================
             */

            if (!empty($row['child_reporting_person_id'])) {

                $child['children'] = getHierarchyChildren(

                    $pdo,

                    $row['child_reporting_person_id'],

                    $companyId,

                    /*
                     * IMPORTANT:
                     *
                     * $visited is passed by VALUE.
                     *
                     * This means each branch gets its own
                     * visited list.
                     */
                    $visited
                );
            }


            /*
             * ==================================================
             * ADD STAFF TO CHILDREN
             * ==================================================
             */

            $children[] = $child;
        }


        /*
         * ======================================================
         * RETURN CHILDREN
         * ======================================================
         */

        return $children;
    }


    /*
     * ==========================================================
     * BUILD ROOT HIERARCHY
     * ==========================================================
     */

    $visited = [];


    $hierarchy = [

        /*
         * Root Staff / Director ID
         */
        'staff_id' =>
        $root['reporting_person'],

        /*
         * Root Name
         */
        'staff_name' =>
        $root['staff_name'],

        /*
         * Root Designation
         */
        'designation_name' =>
        $root['designation_name'] ?? '',

        /*
         * Root Director does not have staff occupation
         * information.
         */
        'branch_name' => '',

        'department_name' => '',

        'team_name' => '',

        /*
         * Get complete hierarchy
         */
        'children' =>
        getHierarchyChildren(

            $pdo,

            $root['reporting_person_id'],

            $root['company_id'],

            $visited
        )

    ];


    /*
     * ==========================================================
     * RETURN SUCCESS RESPONSE
     * ==========================================================
     */

    echo json_encode([

        'status' => true,

        'data' => $hierarchy

    ]);
} catch (Exception $e) {

    /*
     * ==========================================================
     * ERROR RESPONSE
     * ==========================================================
     */

    echo json_encode([

        'status' => false,

        'message' => $e->getMessage()

    ]);
}
