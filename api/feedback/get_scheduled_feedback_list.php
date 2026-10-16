<?php

// Fetch active feedback list for the logged-in user.
// Shows feedback assigned to the user's department and company.
// Marks each feedback as Pending or Completed based on user response.

require '../../ajaxconfig.php';
@session_start();

$user_id = $_SESSION['user_id'];

$ratings_list_arr = array();

$i = 0;

// GET USER DEPARTMENT & COMPANY
$deptQry = $pdo->query("

    SELECT 
        oi.department, 
        oi.company_id,
        oi.branch_id

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
$branch_id = $deptData['branch_id'];

// GET FEEDBACK LIST
$qry = $pdo->query("
    SELECT fc.id, fc.feedback_title
    FROM feedback_titles fc
    WHERE fc.company_id = '$company_id'
      AND fc.feedback_status = 0
      AND NOW() BETWEEN fc.start_date_time AND fc.end_date_time

      AND (
            NOT EXISTS (SELECT 1 FROM feedback_department_mapping d WHERE d.feedback_titles_id = fc.id)
            OR EXISTS  (SELECT 1 FROM feedback_department_mapping d WHERE d.feedback_titles_id = fc.id AND d.department_id = '$department')
      )

      AND (
            NOT EXISTS (SELECT 1 FROM feedback_branch_mapping b WHERE b.feedback_titles_id = fc.id)
            OR EXISTS  (SELECT 1 FROM feedback_branch_mapping b WHERE b.feedback_titles_id = fc.id AND b.branch_id = '$branch_id')
      )
");

// CHECK DATA AVAILABLE
if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        // SERIAL NUMBER
        $row['sno'] = $i + 1;

        // CHECK USER ALREADY ANSWERED
        $checkQry = $pdo->query("
            SELECT id
            FROM staff_sch_feedback
            WHERE feedback_titles_id = '" . $row['id'] . "'
                AND insert_login_id = '$user_id'
        ");
        $alreadyAnswered = $checkQry->rowCount();

        // STATUS & BUTTON
        if ($alreadyAnswered > 0) {

            // COMPLETED
            $row['status'] = 'Completed';
            $row['action'] = ' 
                <button 
                    class="btn btn-secondary"
                    disabled>
                    Completed
                </button>
            ';
        } else {

            // PENDING
            $row['status'] = 'Pending';
            $row['action'] = '
                <button 
                    class="btn btn-primary ratingsAnswerBtn"
                    value="' . $row['id'] . '">
                    Answer
                </button>
            ';
        }
        // STORE ARRAY
        $ratings_list_arr[$i] = $row;
        $i++;
    }
}
// CLOSE CONNECTION
$pdo = null;

// JSON RESPONSE
echo json_encode($ratings_list_arr);
