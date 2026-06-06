<?php

/** Employee Poll List **
 * Purpose:
 * - Retrieves active polls assigned to the logged-in employee.
 * - Filters polls based on employee department and company.
 * - Checks whether the employee has already submitted a poll response.
 * - Displays poll status as Pending or Completed.
 * - Generates action buttons for answering polls.
 * - Returns poll data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

@session_start();

$user_id = $_SESSION['user_id'];

$poll_list_arr = [];

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

/* Get Active Polls */
$stmt = $pdo->prepare("SELECT DISTINCT
        pt.id,
        pt.poll_title
    FROM poll_titles pt
    INNER JOIN poll_department_mapping pdm
        ON pdm.poll_titles_id = pt.id
    WHERE pdm.department_id = ?
    AND pt.company_id = ?
    AND pt.poll_status = ?
    AND NOW() BETWEEN pt.start_date_time AND pt.end_date_time
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
        $checkStmt = $pdo->prepare("SELECT id
            FROM poll_answers
            WHERE poll_titles_id = ?
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
                <button class="btn btn-primary pollAnswerBtn" value="' . $row['id'] . '">
                    Answer
                </button>
            ';
        }

        $poll_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($poll_list_arr);
