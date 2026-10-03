<?php
require "../../ajaxconfig.php";

session_start();

$user_id = $_SESSION['user_id'];
$company_id = $_POST['company_id'] ?? '';

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


// IF DEPARTMENT ONLY OR BOTH
// Branch should show ALL branches
if (empty($mapping_branch)) {

    $qry = $pdo->prepare("
        SELECT id, branch_name
        FROM branch_creation
        WHERE company_id = ?
    ");

    $qry->execute([$company_id]);

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);


// IF BRANCH VALUE EXISTS
// Show only mapped branches
} else {

    $branch_ids = array_filter(
        array_map('trim', explode(',', $mapping_branch))
    );

    if (!empty($branch_ids)) {

        $placeholders = implode(
            ',',
            array_fill(0, count($branch_ids), '?')
        );

        $qry = $pdo->prepare("
            SELECT id, branch_name
            FROM branch_creation
            WHERE id IN ($placeholders)
        ");

        $qry->execute($branch_ids);

        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
}


echo json_encode($result);
?>