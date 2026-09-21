<?php

session_start();
require "../../ajaxconfig.php";

$response = [];

$user_id = $_SESSION['user_id'];

//====================================================
// GET USER APPROVAL TYPES & SCREENS
//====================================================

$stmt = $pdo->prepare("
    SELECT approved_request_type, screens
    FROM users
    WHERE id = ?
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([]);
    exit;
}

$approvedTypes = [];

if (!empty($user['approved_request_type'])) {
    $approvedTypes = array_filter(
        array_map('trim', explode(',', $user['approved_request_type']))
    );
}

$screens = [];

if (!empty($user['screens'])) {
    $screens = array_filter(
        array_map('trim', explode(',', $user['screens']))
    );
}

//====================================================
// REGULARIZATION NOTIFICATIONS
//====================================================

foreach ($approvedTypes as $type) {

    $stmt = $pdo->prepare("
        SELECT
            SUM(DATE(created_date) = CURDATE()) AS today_count,
            COUNT(*) AS total_count
        FROM regularization
        WHERE status = 0
        AND req_type = ?
    ");

    $stmt->execute([$type]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    switch ($type) {
        case 1:
            $requestType = "Leave";
            break;
        case 2:
            $requestType = "Permission";
            break;
        case 3:
            $requestType = "Week Off";
            break;
        case 4:
            $requestType = "OT";
            break;
        default:
            $requestType = "Unknown";
            break;
    }

    $response[] = [
        "module" => "Regularization",   // ⭐ IMPORTANT FIX
        "request_type" => $requestType,
        "req_type" => $type,
        "today_count" => (int)$count['today_count'],
        "total_count" => (int)$count['total_count']
    ];
}

//====================================================
// FEEDBACK NOTIFICATION
//====================================================

if (in_array(23, $screens)) {

    $feedbackPendingCount = 0;

    // Get user's department & company
    $deptQry = $pdo->query("
        SELECT
            oi.department,
            oi.company_id
        FROM users u
        LEFT JOIN occupation_info oi
            ON oi.id = (
                SELECT MAX(id)
                FROM occupation_info
                WHERE staff_profile_id = u.staff_name_id
            )
        WHERE u.id = '$user_id'
    ");

    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $department = $deptData['department'];
    $company_id = $deptData['company_id'];

    // Get active feedback titles
    $feedbackQry = $pdo->query("
        SELECT DISTINCT
            fc.id
        FROM feedback_titles fc
        JOIN feedback_department_mapping fdm
            ON fdm.feedback_titles_id = fc.id
        WHERE fdm.department_id = '$department'
            AND fc.company_id = '$company_id'
            AND fc.feedback_status = 0
            AND NOW() BETWEEN fc.start_date_time
            AND fc.end_date_time
    ");

    while ($feedback = $feedbackQry->fetch(PDO::FETCH_ASSOC)) {

        $checkQry = $pdo->query("
            SELECT id
            FROM staff_sch_feedback
            WHERE feedback_titles_id = '{$feedback['id']}'
            AND insert_login_id = '$user_id'
        ");

        if ($checkQry->rowCount() == 0) {
            $feedbackPendingCount++;
        }
    }

    $response[] = [
        "module" => "Feedback",   // ⭐ IMPORTANT FIX
        "request_type" => "Feedback",
        "req_type" => "FEEDBACK",
        "today_count" => 0,
        "total_count" => $feedbackPendingCount
    ];
}

//====================================================
// RETURN RESPONSE
//====================================================

echo json_encode($response);