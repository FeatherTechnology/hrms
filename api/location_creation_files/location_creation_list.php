<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

/* Get Filters */
$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';

/* =========================================================
   Get Current Logged-in User
   ========================================================= */
$user_stmt = $pdo->prepare("
    SELECT 
        u.staff_name_id,
        u.user_type,
        u.director_name
    FROM users u
    WHERE u.id = ?
");
$user_stmt->execute([$user_id]);
$current_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$my_staff_id = $current_user['staff_name_id'] ?? 0;
$user_type   = $current_user['user_type'] ?? 0;
$director_id = $current_user['director_name'] ?? 0;


/* =========================================================
   Column definitions for DataTables indexing
   ========================================================= */
$column = array(
    'oi.id',
    'sc.staff_id',
    'sc.staff_name',
    'dc.department_name',
    'bc.branch_name',
    'bcs.branch_name',
    'lam.from_date',
    'lam.to_date',
    'lam.no_of_days',
    'lam.lattitude_longitude',
    'oi.id'
);


/* =========================================================
   Base Query Conditions
   ========================================================= */
$base_query = "
    FROM occupation_info oi

    LEFT JOIN branch_creation bc 
        ON oi.branch_id = bc.id 

    LEFT JOIN department_creation dc 
        ON oi.department = dc.id 

    LEFT JOIN staff_creation sc 
        ON oi.staff_profile_id = sc.id

    LEFT JOIN designation_creation des 
        ON oi.designation = des.id

    LEFT JOIN location_access_mapping lam 
        ON lam.id = (
            SELECT id 
            FROM location_access_mapping 
            WHERE staff_profile_id = oi.staff_profile_id 
              AND status = 0
              AND (
                    CURDATE() BETWEEN from_date AND to_date 
                    OR from_date >= CURDATE()
              )
            ORDER BY 
                CASE 
                    WHEN CURDATE() BETWEEN from_date AND to_date 
                    THEN 0 
                    ELSE 1 
                END,
                from_date ASC 
            LIMIT 1
        )

    LEFT JOIN branch_creation bcs 
        ON lam.assigned_branch = bcs.id

    WHERE oi.off_type = 1 

      AND oi.id IN (
          SELECT MAX(id) 
          FROM occupation_info 
          WHERE effective_from <= NOW()
          GROUP BY staff_profile_id
      )

      AND (
            DATE(sc.relieve_date) >= '$today' 
            OR sc.relieve_date = '' 
            OR sc.relieve_date IS NULL
      )
";


/* =========================================================
   REPORTING PERSON ACCESS
   ========================================================= */

/*
    USER TYPE 2
    ---------------------------------------------------------
    Staff / Manager

    Show only staff directly mapped to the logged-in staff.
*/
if ($user_type == 2) {

    $base_query .= "
        AND oi.staff_profile_id IN (

            SELECT rpm.reporting_staff
            FROM reporting_person rp

            INNER JOIN reporting_person_mapping rpm
                ON rpm.reporting_person_id = rp.id

            WHERE rp.user_type = 2
              AND rp.reporting_person = " . intval($my_staff_id) . "
        )
    ";
}


/*
    USER TYPE 1
    ---------------------------------------------------------
    Director

    First get staff directly mapped to the Director.

    Then recursively get staff mapped under those staff.

    Example:

        Director
           |
           +-- Staff A
           |     +-- Staff X
           |     +-- Staff Y
           |
           +-- Staff B
                 +-- Staff Z

    Result:
        Staff A
        Staff B
        Staff X
        Staff Y
        Staff Z
*/
if ($user_type == 1) {

    $base_query .= "
        AND oi.staff_profile_id IN (

            WITH RECURSIVE staff_hierarchy AS (

                /* -----------------------------------------
                   LEVEL 1
                   Staff directly mapped to Director
                   ----------------------------------------- */
                SELECT rpm.reporting_staff AS staff_id

                FROM reporting_person rp

                INNER JOIN reporting_person_mapping rpm
                    ON rpm.reporting_person_id = rp.id

                WHERE rp.user_type = 1
                  AND rp.director_id = " . intval($director_id) . "


                UNION ALL


                /* -----------------------------------------
                   NEXT LEVELS
                   Staff mapped under the previous staff
                   ----------------------------------------- */
                SELECT rpm2.reporting_staff AS staff_id

                FROM staff_hierarchy sh

                INNER JOIN reporting_person rp2
                    ON rp2.user_type = 2
                   AND rp2.reporting_person = sh.staff_id

                INNER JOIN reporting_person_mapping rpm2
                    ON rpm2.reporting_person_id = rp2.id
            )

            SELECT staff_id
            FROM staff_hierarchy
        )
    ";
}


/* =========================================================
   Apply Form Dropdown Filters
   ========================================================= */

if ($company_id != '') {
    $base_query .= "
        AND oi.company_id = " . intval($company_id);
}

if ($branch_id != '') {
    $base_query .= "
        AND oi.branch_id = " . intval($branch_id);
}

if ($department_id != '') {
    $base_query .= "
        AND oi.department = " . intval($department_id);
}


/* =========================================================
   Total Base Records Count
   ========================================================= */

$total_stmt = $pdo->query("
    SELECT COUNT(oi.id)
    " . $base_query
);

$total_records = $total_stmt->fetchColumn();


/* =========================================================
   DataTables Global Text Search
   ========================================================= */

if (isset($_POST['search']) && $_POST['search'] != "") {

    $search = trim($_POST['search']);

    $base_query .= "
        AND (
            sc.staff_id LIKE '$search%'
            OR sc.staff_name LIKE '%$search%'
            OR dc.department_name LIKE '%$search%'
            OR bc.branch_name LIKE '%$search%'
            OR bcs.branch_name LIKE '%$search%'
        )
    ";
}


/* =========================================================
   Filtered Row Count
   ========================================================= */

$filter_stmt = $pdo->query("
    SELECT COUNT(oi.id)
    " . $base_query
);

$number_filter_row = $filter_stmt->fetchColumn();


/* =========================================================
   Order Configuration
   ========================================================= */

if (isset($_POST['order'])) {

    $order_column = intval($_POST['order']['0']['column']);

    $order_direction =
        ($_POST['order']['0']['dir'] === 'desc')
        ? 'DESC'
        : 'ASC';

    if (isset($column[$order_column])) {

        $base_query .= "
            ORDER BY " . $column[$order_column] . " " . $order_direction;
    }

} else {

    $base_query .= " ORDER BY sc.id DESC ";
}


/* =========================================================
   Limit Configuration
   ========================================================= */

$limit = '';

if (
    isset($_POST['length']) &&
    $_POST['length'] != -1
) {

    $limit = "
        LIMIT "
        . intval($_POST['start']) .
        ","
        . intval($_POST['length']);
}


/* =========================================================
   Main Data Query
   ========================================================= */

$main_sql = "
    SELECT 
        oi.id,
        sc.staff_id,
        sc.staff_name,
        dc.department_name,
        bc.branch_name,
        bcs.branch_name AS assigned_branch_name,
        lam.from_date,
        lam.to_date,
        lam.no_of_days,
        lam.lattitude_longitude,
        oi.staff_profile_id

    " . $base_query . "

    " . $limit;


/* =========================================================
   Execute Main Query
   ========================================================= */

$statement = $pdo->query($main_sql);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   Prepare DataTables Data
   ========================================================= */

$data = [];

$sno = intval($_POST['start'] ?? 0) + 1;

foreach ($result as $row) {

    $sub_array = array();

    $sub_array[] = $sno++;

    $sub_array[] = $row['staff_id'];

    $sub_array[] = $row['staff_name'];

    $sub_array[] = $row['department_name'];

    $sub_array[] = $row['branch_name'];

    $sub_array[] = $row['assigned_branch_name'];

    $sub_array[] =
        !empty($row['from_date'])
        ? date('d-m-Y', strtotime($row['from_date']))
        : '';

    $sub_array[] =
        !empty($row['to_date'])
        ? date('d-m-Y', strtotime($row['to_date']))
        : '';

    $sub_array[] = $row['no_of_days'];

    $sub_array[] = $row['lattitude_longitude'];

    $sub_array[] = "
        <span 
            class='icon-border_color locationActionBtn'
            data-id='" . $row['id'] . "'
            data-staff-profile-id='" . $row['staff_profile_id'] . "'
        ></span>
    ";

    $data[] = $sub_array;
}


/* =========================================================
   Output JSON
   ========================================================= */

$output = array(

    "draw" =>
        intval($_POST['draw'] ?? 0),

    "recordsTotal" =>
        intval($total_records),

    "recordsFiltered" =>
        intval($number_filter_row),

    "data" =>
        $data
);

echo json_encode($output);
?>