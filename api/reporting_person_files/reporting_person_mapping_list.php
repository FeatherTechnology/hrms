<?php

require '../../ajaxconfig.php';


/*
 * ==========================================================
 * DATATABLE COLUMNS
 * ==========================================================
 */

$column = array(
    'rp.id',
    'cc.company_name',
    'rp.user_type',
    'dc.designation',
    'sc.staff_name',
    'dir.director_name',
    'reporting_staff_names',
    'rp.id',
    'rp.id'
);


/*
 * ==========================================================
 * USER TYPE
 * ==========================================================
 */

$user_type = [
    1 => 'Director',
    2 => 'Staff'
];


/*
 * ==========================================================
 * BASE QUERY
 * ==========================================================
 *
 * reporting_person_mapping.reporting_staff
 * contains only staff_creation.id
 *
 * Example:
 *
 * reporting_staff = 11
 * reporting_staff = 16
 * reporting_staff = 6
 *
 * We join staff_creation using:
 *
 * sc2.id = rpm.reporting_staff
 *
 */

$query = "SELECT
        rp.id AS reporting_person_id,

        cc.company_name,

        rp.user_type,

        dc.designation,

        sc.staff_name AS reporting_person,

        dir.director_name,

        GROUP_CONCAT(
    CONCAT(
        sc2.staff_name,
        ' - (',
        COALESCE(bc2.branch_name, ''),
        ' - ',
        COALESCE(dc2.department_name, ''),
        ' - ',
        COALESCE(tn2.team_name, ''),
        ' - ',
        COALESCE(dsg2.designation, ''),
        ')'
    )
    ORDER BY sc2.staff_name ASC
    SEPARATOR '||'
) AS reporting_staff_names

    FROM reporting_person rp

    LEFT JOIN company_creation cc
        ON cc.id = rp.company_id

    LEFT JOIN staff_creation sc
        ON sc.id = rp.reporting_person
        AND sc.company_id = rp.company_id

    LEFT JOIN designation_creation dc
        ON dc.id = rp.designation

    LEFT JOIN director_creation dir
        ON dir.id = rp.director_id

    LEFT JOIN reporting_person_mapping rpm
        ON rpm.reporting_person_id = rp.id

    /*
     * Reporting Staff
     */
    LEFT JOIN staff_creation sc2
        ON sc2.id = rpm.reporting_staff
        AND sc2.company_id = rp.company_id

    /*
     * Latest occupation information
     */
    LEFT JOIN occupation_info oi2
        ON oi2.id = (
            SELECT MAX(oi3.id)
            FROM occupation_info oi3
            WHERE oi3.staff_profile_id = sc2.id
        )

    /*
     * Branch
     */
    LEFT JOIN branch_creation bc2
        ON bc2.id = oi2.branch_id

    /*
     * Department
     */
    LEFT JOIN department_creation dc2
        ON dc2.id = oi2.department

    /*
     * Team
     */
    LEFT JOIN team_name_creation tn2
        ON tn2.id = oi2.team

        /*
 * Designation
 */
LEFT JOIN designation_creation dsg2
    ON dsg2.id = oi2.designation

    WHERE 1
";


/*
 * ==========================================================
 * SEARCH
 * ==========================================================
 */

if (isset($_POST['search']) && $_POST['search'] != '') {

    $search = trim($_POST['search']);

    $search = $pdo->quote('%' . $search . '%');

    $query .= "
        AND (
            cc.company_name LIKE $search

            OR dc.designation LIKE $search

            OR sc.staff_name LIKE $search

            OR dir.director_name LIKE $search

            OR sc2.staff_name LIKE $search

            OR bc2.branch_name LIKE $search

            OR dc2.department_name LIKE $search

            OR tn2.team_name LIKE $search
        )
    ";
}


/*
 * ==========================================================
 * GROUP BY
 * ==========================================================
 */

$query .= "
    GROUP BY
        rp.id,
        cc.company_name,
        rp.user_type,
        dc.designation,
        sc.staff_name,
        dir.director_name
";


/*
 * ==========================================================
 * ORDER BY
 * ==========================================================
 */

if (isset($_POST['order'])) {

    $order_column = intval($_POST['order'][0]['column']);

    $order_dir = strtolower($_POST['order'][0]['dir']) === 'desc'
        ? 'DESC'
        : 'ASC';

    if (isset($column[$order_column])) {

        /*
         * reporting_staff_names is an alias.
         * MySQL allows alias in ORDER BY.
         */
        $query .= "
            ORDER BY {$column[$order_column]} $order_dir
        ";
    }
} else {

    $query .= "
        ORDER BY rp.id DESC
    ";
}


/*
 * ==========================================================
 * FILTERED RECORD COUNT
 * ==========================================================
 */

$statement = $pdo->prepare($query);

$statement->execute();

$filtered_rows = $statement->fetchAll(PDO::FETCH_ASSOC);

$number_filter_row = count($filtered_rows);


/*
 * ==========================================================
 * PAGINATION
 * ==========================================================
 */

$query1 = '';

if (
    isset($_POST['length']) &&
    intval($_POST['length']) != -1
) {

    $start = isset($_POST['start'])
        ? intval($_POST['start'])
        : 0;

    $length = intval($_POST['length']);

    $query1 = "
        LIMIT $start, $length
    ";
}


/*
 * ==========================================================
 * FINAL QUERY
 * ==========================================================
 */

$statement = $pdo->prepare(
    $query . $query1
);

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);


/*
 * ==========================================================
 * DATATABLE DATA
 * ==========================================================
 */

$sno = isset($_POST['start'])
    ? intval($_POST['start']) + 1
    : 1;

$data = [];


foreach ($result as $row) {

    $sub_array = [];


    /*
     * ======================================================
     * S.NO
     * ======================================================
     */

    $sub_array[] = $sno++;


    /*
     * ======================================================
     * COMPANY NAME
     * ======================================================
     */

    $sub_array[] = htmlspecialchars(
        $row['company_name'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    /*
 * ======================================================
 * USER TYPE
 * ======================================================
 */

    $sub_array[] =
        $user_type[$row['user_type']] ?? '';


    /*
 * ======================================================
 * REPORTING PERSON
 * ======================================================
 */

    if ((int)$row['user_type'] === 1) {

        // Director
        $reporting_person = $row['director_name'] ?? '';
    } else {

        // Staff
        $reporting_person = $row['reporting_person'] ?? '';
    }

    $sub_array[] = htmlspecialchars(
        $reporting_person,
        ENT_QUOTES,
        'UTF-8'
    );

    /*
     * ======================================================
     * DESIGNATION
     * ======================================================
     */

    $sub_array[] = htmlspecialchars(
        $row['designation'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    /*
 * ======================================================
 * REPORTING STAFF
 * ======================================================
 */

    $staff_list = '';

    if (!empty($row['reporting_staff_names'])) {

        $staff_array = explode(
            '||',
            $row['reporting_staff_names']
        );

        $staff_array = array_filter(
            $staff_array,
            function ($value) {
                return trim($value) !== '';
            }
        );

        $formatted_staff = [];

        foreach ($staff_array as $staff) {

            $staff = trim($staff);

            /*
         * Expected:
         *
         * Sundar - (Vandavasi - Collection - Team G - General Manager)
         */

            if (preg_match('/^(.*?)\s*-\s*(\(.*\))$/', $staff, $matches)) {

                $staff_name = htmlspecialchars(
                    trim($matches[1]),
                    ENT_QUOTES,
                    'UTF-8'
                );

                $staff_details = trim(
                    $matches[2],
                    '()'
                );

                /*
     * Split details:
     * Vandavasi - Collection - Team G - General Manager
     */

                $details = explode(' - ', $staff_details);

                $branch = htmlspecialchars(
                    $details[0] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $department = htmlspecialchars(
                    $details[1] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $team = htmlspecialchars(
                    $details[2] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $designation = htmlspecialchars(
                    $details[3] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );

                $formatted_staff[] = '
        <div style="margin-bottom:4px; white-space:nowrap;">

            <span style="color:#f26b35; font-weight:750;">
                ' . $staff_name . '
            </span>

            <span style="color:#f26b35;">
                &nbsp;(
            </span>

            <span>
                ' . $branch . ' - 
                ' . $department . ' - 
                ' . $team . ' - 
            </span>

            <span style="font-weight:750;">
                ' . $designation . '
            </span>

            <span style="color:#f26b35;">
                )
            </span>

        </div>
    ';
            } else {

                $formatted_staff[] = '
                <div style="margin-bottom:4px;">
                    <span style="color:#f26b35;">
                        ' . htmlspecialchars(
                    $staff,
                    ENT_QUOTES,
                    'UTF-8'
                ) . '
                    </span>
                </div>
            ';
            }
        }

        /*
     * No comma.
     * Each staff will appear in a separate line.
     */
        $staff_list = implode('', $formatted_staff);
    }

    $sub_array[] = $staff_list;


    /*
     * ======================================================
     * HIERARCHY
     * ======================================================
     */

    $hierarchy = '
        <span
            class="reportingPersonHierarchyBtn"
            value="' . (int)$row['reporting_person_id'] . '"
            title="View Hierarchy"
            style="cursor:pointer;">
            <i class="icon-eye"></i>
        </span>
    ';

    $sub_array[] = $hierarchy;


    /*
     * ======================================================
     * ACTION
     * ======================================================
     */

    $action = "
        <span
            class='icon-border_color reportingPersonActionBtn'
            value='" . (int)$row['reporting_person_id'] . "'>
        </span>
    ";

    $sub_array[] = $action;


    /*
     * ======================================================
     * ADD ROW
     * ======================================================
     */

    $data[] = $sub_array;
}


/*
 * ==========================================================
 * TOTAL RECORD COUNT
 * ==========================================================
 */

function count_all_data($pdo)
{
    $query = "
        SELECT COUNT(*)
        FROM reporting_person
    ";

    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}


/*
 * ==========================================================
 * DATATABLE RESPONSE
 * ==========================================================
 */

$output = [

    'draw' => isset($_POST['draw'])
        ? intval($_POST['draw'])
        : 0,

    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
];


echo json_encode($output);
