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

// FEEDBACK NOTIFICATION
if (in_array(24, $screens)) {

    $feedbackPendingCount = 0;
    $feedbackTodayCount = 0;

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
                AND effective_from <= NOW()
            )
        WHERE u.id = '$user_id'
    ");

    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $department = $deptData['department'];
    $company_id = $deptData['company_id'];

    // Get feedback titles
    $feedbackQry = $pdo->query("
        SELECT DISTINCT
            fc.id,
            fc.start_date_time,
            fc.end_date_time
        FROM feedback_titles fc
        JOIN feedback_department_mapping fdm
            ON fdm.feedback_titles_id = fc.id
        WHERE fdm.department_id = '$department'
        AND fc.company_id = '$company_id'
        AND fc.feedback_status = 0
    ");

    while ($feedback = $feedbackQry->fetch(PDO::FETCH_ASSOC)) {

        // Check whether user already answered
        $checkQry = $pdo->query("
            SELECT id
            FROM staff_sch_feedback
            WHERE feedback_titles_id = '{$feedback['id']}'
            AND insert_login_id = '$user_id'
        ");

        // Only unanswered feedback is pending
        if ($checkQry->rowCount() == 0) {

            // Total pending count
            $feedbackPendingCount++;

            // TODAY COUNT
            // Current date/time is between start and end
            if (
                date('Y-m-d H:i:s') >= $feedback['start_date_time'] &&
                date('Y-m-d H:i:s') <= $feedback['end_date_time']
            ) {
                $feedbackTodayCount++;
            }
        }
    }

    $response[] = [
        "module" => "Feedback",
        "request_type" => "Feedback",
        "req_type" => "FEEDBACK",
        "today_count" => $feedbackTodayCount,
        "total_count" => $feedbackPendingCount
    ];
}

//notification for rating 
if (in_array(25, $screens)) {

    $ratingPendingCount = 0;
    $ratingTodayCount = 0;

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
                AND effective_from <= NOW()
            )
        WHERE u.id = '$user_id'
    ");

    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $department = $deptData['department'];
    $company_id = $deptData['company_id'];

    // Get all rating titles
    $ratingbackQry = $pdo->query("
        SELECT DISTINCT
            rt.id,
            rt.start_date_time,
            rt.end_date_time
        FROM rating_titles rt
        JOIN rating_department_mapping rdm
            ON rdm.rating_titles_id = rt.id
        WHERE rdm.department_id = '$department'
            AND rt.company_id = '$company_id'
            AND rt.rating_status = 0
    ");
    $currentDateTime = date('Y-m-d H:i:s');

    while ($rating = $ratingbackQry->fetch(PDO::FETCH_ASSOC)) {

        // Check whether user already answered
        $checkQry = $pdo->query("
            SELECT id
            FROM rating_answers
            WHERE rating_titles_id = '{$rating['id']}'
            AND insert_login_id = '$user_id'
        ");

        // Only unanswered rating
        if ($checkQry->rowCount() == 0) {

            // Total pending rating
            $ratingPendingCount++;

            // Today count
            // Current date/time is between start and end
            if (
                 $currentDateTime >= $rating['start_date_time'] &&
                 $currentDateTime <= $rating['end_date_time']
            ) {
                $ratingTodayCount++;
            }
        }
    }

    $response[] = [
        "module" => "rating",
        "request_type" => "rating",
        "req_type" => "rating",
        "today_count" => $ratingTodayCount,
        "total_count" => $ratingPendingCount
    ];
}

// notification for poll
if (in_array(26, $screens)) {

    $pollPendingCount = 0;
    $pollTodayCount = 0;

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
                AND effective_from <= NOW()
            )
        WHERE u.id = '$user_id'
    ");

    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $department = $deptData['department'];
    $company_id = $deptData['company_id'];

    // Get all poll titles
    $pollbackQry = $pdo->query("
        SELECT DISTINCT
            pt.id,
            pt.start_date_time,
            pt.end_date_time
        FROM poll_titles pt
        JOIN poll_department_mapping pdm
            ON pdm.poll_titles_id = pt.id
        WHERE pdm.department_id = '$department'
            AND pt.company_id = '$company_id'
            AND pt.poll_status = 0
    ");

    // Current date & time
    $currentDateTime = date('Y-m-d H:i:s');

    while ($poll = $pollbackQry->fetch(PDO::FETCH_ASSOC)) {

        // Check whether user already answered
        $checkQry = $pdo->query("
            SELECT id
            FROM poll_answers
            WHERE poll_titles_id = '{$poll['id']}'
            AND insert_login_id = '$user_id'
        ");

        // Only unanswered poll
        if ($checkQry->rowCount() == 0) {

            // Total pending poll
            $pollPendingCount++;

            // Today count
            // Current date/time is between start and end
            if (
                $currentDateTime >= $poll['start_date_time'] &&
                $currentDateTime <= $poll['end_date_time']
            ) {
                $pollTodayCount++;
            }
        }
    }

    $response[] = [
        "module" => "poll",
        "request_type" => "poll",
        "req_type" => "poll",
        "today_count" => $pollTodayCount,
        "total_count" => $pollPendingCount
    ];
}

//====================================================
// RETURN RESPONSE
//====================================================

echo json_encode($response);