<?php
require "../../ajaxconfig.php";

session_start();

$user_id = $_SESSION['user_id'];

$company_id = $_POST['company_id'] ?? '';
$selected_dept = $_POST['selected_dept'] ?? '';

$result = array();


// GET USER MAPPING
$getUser = $pdo->prepare("
    SELECT 
        mapping_branch,
        mapping_department
    FROM users
    WHERE id = ?
");

$getUser->execute([$user_id]);

$userInfo = $getUser->fetch(PDO::FETCH_ASSOC);

$mapping_branch = trim($userInfo['mapping_branch'] ?? '');
$mapping_department = trim($userInfo['mapping_department'] ?? '');


// IF BRANCH ONLY OR BOTH
// Department should show ALL departments
if (empty($mapping_department)) {

    $qry = $pdo->prepare("
        SELECT DISTINCT
            di.id,
            di.department_name
        FROM department_creation di
        LEFT JOIN company_department_mapping cd
            ON di.id = cd.department_id
        WHERE di.department_status = 0
        AND (
            cd.company_id = ?
            OR di.id = ?
        )
    ");

    $qry->execute([
        $company_id,
        $selected_dept
    ]);

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);


// IF DEPARTMENT VALUE EXISTS
// Show only mapped departments
} else {

    $department_ids = array_filter(
        array_map('trim', explode(',', $mapping_department))
    );

    if (!empty($department_ids)) {

        $placeholders = implode(
            ',',
            array_fill(0, count($department_ids), '?')
        );

        $params = $department_ids;

        $sql = "
            SELECT DISTINCT
                di.id,
                di.department_name
            FROM department_creation di
            WHERE di.department_status = 0
            AND di.id IN ($placeholders)
        ";

        $qry = $pdo->prepare($sql);
        $qry->execute($params);

        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
}


echo json_encode($result);
?>