<?php

// Fetch staff feedback based on the logged-in user's Feedback Access Type.

include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'];

$result = [];

// Get the logged-in user's feedback access type
$userQry = $pdo->prepare("SELECT feedback_access_type FROM users WHERE id = :user_id AND status = 0");
$userQry->execute([':user_id' => $user_id]);

$user = $userQry->fetch(PDO::FETCH_ASSOC);

if ($user) {

    $sql = "SELECT
            gf.feedback_name,
            sgf.commants,
            u.user_name AS visible_to,
            us.user_name AS submitted_by,
            u.feedback_access_type
        FROM staff_general_feedback sgf
        LEFT JOIN general_feedback gf ON gf.id = sgf.general_feedback_id
        LEFT JOIN users u ON u.id = sgf.user_id
        LEFT JOIN users us ON us.id = sgf.insert_login_id
        WHERE u.status = 0";

    // If Feedback Access Type = Individual, show only the logged-in user's records
    if ($user['feedback_access_type'] == '2') {
        $sql .= " AND sgf.user_id = :user_id";
    }

    $qry = $pdo->prepare($sql);

    if ($user['feedback_access_type'] == '2') {
        $qry->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    }

    $qry->execute();

    if ($qry->rowCount() > 0) {
        $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
}

$pdo = null;

echo json_encode($result);
