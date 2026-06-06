<?php

/** Employee Rating List **
 * Purpose:
 * - Retrieves active ratings assigned to the logged-in employee.
 * - Filters ratings based on employee department and company.
 * - Checks whether the employee has already submitted a rating.
 * - Displays rating status as Pending or Completed.
 * - Generates action buttons for answering ratings.
 * - Returns rating data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

@session_start();

$user_id = $_SESSION['user_id'];

$ratings_list_arr = [];

$i = 0;

/* Get Employee Department and Company */
$deptStmt = $pdo->prepare("SELECT
        oi.department,
        oi.company_id
    FROM users u
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = u.staff_name_id
        )
    WHERE u.id = ?
");

$deptStmt->execute([$user_id]);

$deptData = $deptStmt->fetch(PDO::FETCH_ASSOC);

$department = $deptData['department'] ?? '';
$company_id = $deptData['company_id'] ?? '';

/* Get Active Ratings */
$stmt = $pdo->prepare("SELECT DISTINCT
        rt.id,
        rt.rating_title
    FROM rating_titles rt
    INNER JOIN rating_department_mapping rdm
        ON rdm.rating_titles_id = rt.id
    WHERE rdm.department_id = ?
    AND rt.company_id = ?
    AND rt.rating_status = ?
    AND NOW() BETWEEN rt.start_date_time AND rt.end_date_time
");

$stmt->execute([
    $department,
    $company_id,
    0
]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Serial Number
        $row['sno'] = $i + 1;

        /* Check Whether Already Answered */
        $checkStmt = $pdo->prepare("
            SELECT id
            FROM rating_answers
            WHERE rating_titles_id = ?
            AND insert_login_id = ?
        ");

        $checkStmt->execute([
            $row['id'],
            $user_id
        ]);

        $alreadyAnswered = $checkStmt->rowCount();

        // Status and Action
        if ($alreadyAnswered > 0) {

            $row['status'] = 'Completed';
            $row['action'] = '
                <button class="btn btn-secondary" disabled>
                    Completed
                </button>
            ';
        } else {

            $row['status'] = 'Pending';
            $row['action'] = '
                <button class="btn btn-primary ratingsAnswerBtn" value="' . $row['id'] . '">
                    Answer
                </button>
            ';
        }

        $ratings_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($ratings_list_arr);
