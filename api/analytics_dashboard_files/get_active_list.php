<?php

require '../../ajaxconfig.php';
@session_start();

$user_id = $_SESSION['user_id'];
$type    = $_POST['type'] ?? '';

$result_arr = [];
$i = 0;

// GET USER DETAILS
$userQry = $pdo->query("
    SELECT
        user_type,
        director_company,
        staff_name_id
    FROM users
    WHERE id = '$user_id'
");

$userData = $userQry->fetch(PDO::FETCH_ASSOC);

$user_type        = $userData['user_type'];
$director_company = $userData['director_company'];
$staff_name_id    = $userData['staff_name_id'];

$company_condition = '';

// DIRECTOR
if ($user_type == 1) {

    $company_condition = " tt.company_id IN ($director_company) ";
}
// STAFF
else {

    $deptQry = $pdo->query("
        SELECT
            department,
            company_id
        FROM occupation_info
        WHERE id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = '$staff_name_id'
        )
    ");

    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $company_id = $deptData['company_id'];

    $company_condition = " tt.company_id = '$company_id' ";
}

// CONFIGURATION
$config = [

    'feedback' => [
        'title_table'   => 'feedback_titles',
        'mapping_table' => 'feedback_department_mapping',
        'answer_table'  => 'staff_sch_feedback',
        'id_column'     => 'feedback_titles_id',
        'title_column'  => 'feedback_title',
        'status_column' => 'feedback_status'
    ],

    'rating' => [
        'title_table'   => 'rating_titles',
        'mapping_table' => 'rating_department_mapping',
        'answer_table'  => 'rating_answers',
        'id_column'     => 'rating_titles_id',
        'title_column'  => 'rating_title',
        'status_column' => 'rating_status'
    ],

    'poll' => [
        'title_table'   => 'poll_titles',
        'mapping_table' => 'poll_department_mapping',
        'answer_table'  => 'poll_answers',
        'id_column'     => 'poll_titles_id',
        'title_column'  => 'poll_title',
        'status_column' => 'poll_status'
    ]

];

if (!isset($config[$type])) {
    echo json_encode([]);
    exit;
}

$c = $config[$type];

// MAIN QUERY
$qry = $pdo->query("
    SELECT DISTINCT
        tt.id,
        tt.{$c['title_column']} AS title,
        tt.start_date_time,
        tt.end_date_time
    FROM {$c['title_table']} tt
    JOIN {$c['mapping_table']} dm

        ON dm.{$c['id_column']} = tt.id
    WHERE
        $company_condition
        AND tt.{$c['status_column']} = 0
        AND NOW() <= tt.end_date_time
");

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
    // TOTAL RESPONSE
    $responseQry = $pdo->query("
        SELECT COUNT(DISTINCT insert_login_id) total_response
        FROM {$c['answer_table']}
        WHERE {$c['id_column']} = '{$row['id']}'
    ");
    $response = $responseQry->fetch(PDO::FETCH_ASSOC);
    // TOTAL STAFF
    $staffQry = $pdo->query("

        SELECT COUNT(DISTINCT oi.staff_profile_id) total_staff
        FROM {$c['mapping_table']} dm
        INNER JOIN occupation_info oi
            ON oi.department = dm.department_id
        WHERE dm.{$c['id_column']} = '{$row['id']}'
    ");

    $staff = $staffQry->fetch(PDO::FETCH_ASSOC);

    // POLL TOP ANSWER
    $top_answer = '';
    // RATING AVERAGE
    $average_rating = '';
    if ($type == 'poll') {
        $pollQry = $pdo->query("
            SELECT
                po.poll_options AS answer,
                COUNT(pa.poll_value) AS answer_count
            FROM {$c['answer_table']} pa
            INNER JOIN poll_options_mapping po
                ON po.id = pa.poll_value
            WHERE pa.{$c['id_column']} = '{$row['id']}'
            GROUP BY pa.poll_value
            ORDER BY answer_count DESC
        ");

        $pollResults = $pollQry->fetchAll(PDO::FETCH_ASSOC);
        if ($pollResults) {
            // Highest count
            $maxCount = $pollResults[0]['answer_count'];
            $winner = [];
            foreach ($pollResults as $poll) {
                if ($poll['answer_count'] == $maxCount) {
                    $winner[] =
                        $poll['answer'] .
                        " (" .
                        $poll['answer_count'] .
                        ")";
                }
            }
            // Convert array to single column text
            $top_answer = implode(', ', $winner);
        }
    }

    // RATING AVERAGE

    if ($type == 'rating') {
        $ratingQry = $pdo->query("
        SELECT 
            ROUND(AVG(rating_value),2) AS avg_rating
        FROM {$c['answer_table']}
        WHERE {$c['id_column']} = '{$row['id']}'
    ");
        $ratingData = $ratingQry->fetch(PDO::FETCH_ASSOC);
        if ($ratingData['avg_rating'] != null) {
            $average_rating = $ratingData['avg_rating'];
        }
    }

    $result_arr[] = [
        'sno' => ++$i,
        'id' => $row['id'],
        'title' => $row['title'],
        'start_date' => date(
            'd-m-Y H:i:s',
            strtotime($row['start_date_time'])
        ),
        'end_date' => date(
            'd-m-Y H:i:s',
            strtotime($row['end_date_time'])
        ),
        'total_staff' => $staff['total_staff'],
        'total_response' => $response['total_response'],
        'pending' =>
        $staff['total_staff'] - $response['total_response'],
        'top_answer' => $top_answer,
        'average_rating' => $average_rating
    ];
}

echo json_encode($result_arr);
$pdo = null;