<?php
include '../../ajaxconfig.php';
session_start();

$userid  = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";

$result = array();

if ($userid != "") {

    // Step 1: Get company_id from users table
    $userQry = $pdo->query("SELECT company_id FROM users WHERE id = '$userid'");
    $userData = $userQry->fetch(PDO::FETCH_ASSOC);

    $cmpy_id = isset($userData['company_id']) ? $userData['company_id'] : 0;

    // Step 2: Use company_id in main query
    if ($cmpy_id != 0) {

        $qry = $pdo->query("
            SELECT feedback_name, id
            FROM general_feedback 
            WHERE status = 0 
            AND company_id = '$cmpy_id'
        ");

        if ($qry->rowCount() > 0) {
            $result = $qry->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

$pdo = null;
echo json_encode($result);
?>