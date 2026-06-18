<?php
// Retrieve active Feedback, Rating, and Poll list with total response counts based on the current date and company.

require '../../ajaxconfig.php';
@session_start();

$user_id = $_SESSION['user_id'];
$type    = $_POST['type'] ?? '';

$result_arr = [];
$i = 0;

// GET USER DEPARTMENT & COMPANY
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
        tt.{$c['title_column']} as title,
        tt.start_date_time,
        tt.end_date_time

    FROM {$c['title_table']} tt

    JOIN {$c['mapping_table']} dm
        ON dm.{$c['id_column']} = tt.id

    WHERE  tt.company_id = '$company_id'
        AND tt.{$c['status_column']} = 0
        AND NOW() <= tt.end_date_time
");

// RESPONSE
while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $responseQry = $pdo->query("
        SELECT COUNT(DISTINCT insert_login_id) total_response
        FROM {$c['answer_table']}
        WHERE {$c['id_column']} = '{$row['id']}'
    ");

    $response = $responseQry->fetch(PDO::FETCH_ASSOC);

    $result_arr[] = [
        'sno'            => ++$i,
        'id'             => $row['id'],
        'title'          => $row['title'],
        'start_date'     => date('d-m-Y H:i:s', strtotime($row['start_date_time'])),
        'end_date'       => date('d-m-Y H:i:s', strtotime($row['end_date_time'])),
        'total_response' => $response['total_response']
    ];
}

echo json_encode($result_arr);

$pdo = null;