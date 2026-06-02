<?php

require '../../ajaxconfig.php';

@session_start();

$user_id = $_SESSION['user_id'];

$poll_list_arr = array();

$i = 0;


// Get User Department & Company
$deptQry = $pdo->query("SELECT 
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

// Get poll
$qry = $pdo->query("SELECT DISTINCT 
        pt.id,
        pt.poll_title

    FROM poll_titles pt
    JOIN poll_department_mapping pdm ON pdm.poll_titles_id = pt.id
    WHERE pdm.department_id = '$department'AND pt.company_id = '$company_id'
    AND pt.poll_status = 0 AND NOW() BETWEEN pt.start_date_time AND pt.end_date_time
");


if ($qry->rowCount() > 0) {

    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

        // Serial Number
        $row['sno'] = $i + 1;

        // Check Already Answered
        $checkQry = $pdo->query("SELECT id FROM poll_answers
            WHERE poll_titles_id = '" . $row['id'] . "' AND insert_login_id = '$user_id' ");

        $alreadyAnswered = $checkQry->rowCount();

        // Status & Action
        if ($alreadyAnswered > 0) {

            $row['status'] = 'Completed';
            $row['action'] = '<button class="btn btn-secondary" disabled> Completed </button>';
        } else {

            $row['status'] = 'Pending';
            $row['action'] = '<button class="btn btn-primary pollAnswerBtn" value="' . $row['id'] . '"> Answer </button>';
        }

        $poll_list_arr[$i] = $row;
        $i++;
    }
}

$pdo = null;

echo json_encode($poll_list_arr);
