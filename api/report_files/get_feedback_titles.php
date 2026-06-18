<?php
require "../../ajaxconfig.php";
@session_start();

$company_id   = $_POST['company_id'] ?? '';
$department   = $_POST['department_id'] ?? '';
$feedback_type = $_POST['feedback_type'] ?? '';

$response = [];

if ($feedback_type == 1) { // General Feedback

    $stmt = $pdo->prepare("SELECT
            id,
            feedback_name AS title
        FROM general_feedback
        WHERE status = 0
        AND company_id = ?
    ");

    $stmt->execute([$company_id]);
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($feedback_type == 2) { // Feedback Configuration

    $stmt = $pdo->prepare("SELECT DISTINCT
            fc.id,
            fc.feedback_title AS title
        FROM feedback_titles fc
        INNER JOIN feedback_department_mapping fdm
            ON fdm.feedback_titles_id = fc.id
        WHERE fdm.department_id = ?
        AND fc.company_id = ?
        AND fc.feedback_status = 0
        AND NOW() <= fc.end_date_time
    ");

    $stmt->execute([$department, $company_id]);
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($feedback_type == 3) { // Rating

    $stmt = $pdo->prepare("SELECT DISTINCT
            rt.id,
            rt.rating_title AS title
        FROM rating_titles rt
        INNER JOIN rating_department_mapping rdm
            ON rdm.rating_titles_id = rt.id
        WHERE rdm.department_id = ?
        AND rt.company_id = ?
        AND rt.rating_status = 0
        AND NOW() <= rt.end_date_time
    ");

    $stmt->execute([$department, $company_id]);
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($feedback_type == 4) { // Poll

    $stmt = $pdo->prepare("SELECT DISTINCT
            pt.id,
            pt.poll_title AS title
        FROM poll_titles pt
        INNER JOIN poll_department_mapping pdm
            ON pdm.poll_titles_id = pt.id
        WHERE pdm.department_id = ?
        AND pt.company_id = ?
        AND pt.poll_status = 0
        AND NOW() <= pt.end_date_time
    ");

    $stmt->execute([$department, $company_id]);
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($response);
