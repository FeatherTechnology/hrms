<?php
require "../../ajaxconfig.php";

$company_id = $_POST['company_id'];
$designation_level = $_POST['designation_level'];

$result = array();

/*
|--------------------------------------------------------------------------
| Get Directors
|--------------------------------------------------------------------------
*/
$directorQry = $pdo->query("
    SELECT
        id,
        director_name AS staff_name,
        'Director' AS designation,
        0 AS designation_level
    FROM director_creation
    ORDER BY director_name ASC
");

$directors = [];

if ($directorQry->rowCount() > 0) {
    $directors = $directorQry->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Get Staff Below Selected Designation Level
|--------------------------------------------------------------------------
*/
$staffQry = $pdo->query("
    SELECT
        sc.id,
        sc.staff_name,
        di.designation,
        di.designation_level
    FROM staff_creation sc

    INNER JOIN occupation_info oi
        ON oi.staff_profile_id = sc.id

    INNER JOIN designation_creation di
        ON di.id = oi.designation

    WHERE oi.id = (
        SELECT MAX(oi2.id)
        FROM occupation_info oi2
        WHERE oi2.staff_profile_id = sc.id
    )

    AND oi.company_id = '$company_id'
    AND di.designation_level < '$designation_level'
    AND sc.status = 1

    ORDER BY di.designation_level ASC, sc.staff_name ASC
");

$staff = [];

if ($staffQry->rowCount() > 0) {
    $staff = $staffQry->fetchAll(PDO::FETCH_ASSOC);
}

/*
|--------------------------------------------------------------------------
| Merge Directors + Staff
|--------------------------------------------------------------------------
*/
$result = array_merge($directors, $staff);

echo json_encode($result);

$pdo = null;
?>