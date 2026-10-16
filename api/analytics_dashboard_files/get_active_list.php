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
$staff_branch_id = '';
$staff_dept_id = '';

// DIRECTOR
if ($user_type == 1) {
    $company_condition = " tt.company_id IN ($director_company) ";
}
// STAFF (Get current occupation based on effective_from and id descending)
else {
    $deptQry = $pdo->prepare("SELECT department,branch_id,company_id
        FROM occupation_info
        WHERE staff_profile_id = ?
        ORDER BY effective_from DESC, id DESC
        LIMIT 1");
    $deptQry->execute([$staff_name_id]);
    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    $company_id      = $deptData['company_id'] ?? '';
    $staff_branch_id = $deptData['branch_id'] ?? '';
    $staff_dept_id   = $deptData['department'] ?? '';

    $company_condition = " tt.company_id = '$company_id' ";
}

// CONFIGURATION
$config = [

    'feedback' => [
        'title_table'   => 'feedback_titles',
        'mapping_table' => 'feedback_department_mapping',
        'branch_table'  => 'feedback_branch_mapping',
        'answer_table'  => 'staff_sch_feedback',
        'id_column'     => 'feedback_titles_id',
        'title_column'  => 'feedback_title',
        'status_column' => 'feedback_status'
    ],

    'rating' => [
        'title_table'   => 'rating_titles',
        'mapping_table' => 'rating_department_mapping',
        'branch_table'  => 'rating_branch_mapping',
        'answer_table'  => 'rating_answers',
        'id_column'     => 'rating_titles_id',
        'title_column'  => 'rating_title',
        'status_column' => 'rating_status'
    ],

    'poll' => [
        'title_table'   => 'poll_titles',
        'mapping_table' => 'poll_department_mapping',
        'branch_table'  => 'poll_branch_mapping',
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

// MAIN QUERY - Fetch active titles for the company
$qry = $pdo->query("SELECT DISTINCT
        tt.id,
        tt.{$c['title_column']} AS title,
        tt.start_date_time,
        tt.end_date_time
    FROM {$c['title_table']} tt
    WHERE
        $company_condition
        AND tt.{$c['status_column']} = 0
        AND NOW() <= tt.end_date_time
");
while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
    $title_id = $row['id'];

    // Check if branch mappings and/or department mappings exist for this title
    $branchCheckQry = $pdo->prepare("SELECT branch_id FROM {$c['branch_table']} WHERE {$c['id_column']} = ?");
    $branchCheckQry->execute([$title_id]);
    $mapped_branches = $branchCheckQry->fetchAll(PDO::FETCH_COLUMN);

    $deptCheckQry = $pdo->prepare("SELECT department_id FROM {$c['mapping_table']} WHERE {$c['id_column']} = ?");
    $deptCheckQry->execute([$title_id]);
    $mapped_depts = $deptCheckQry->fetchAll(PDO::FETCH_COLUMN);

    $has_branches = count($mapped_branches) > 0;
    $has_depts    = count($mapped_depts) > 0;

    // For Staff: Check if this title applies to them based on your rules
    if ($user_type != 1) {
        $is_applicable = false;

        if ($has_branches && $has_depts) {
            // Both selected -> Must match branch AND department
            if (in_array($staff_branch_id, $mapped_branches) && in_array($staff_dept_id, $mapped_depts)) {
                $is_applicable = true;
            }
        } elseif ($has_branches && !$has_depts) {
            // Branch selected, Department not selected -> Must match branch
            if (in_array($staff_branch_id, $mapped_branches)) {
                $is_applicable = true;
            }
        } elseif (!$has_branches && $has_depts) {
            // Branch not selected, Department selected -> Must match department
            if (in_array($staff_dept_id, $mapped_depts)) {
                $is_applicable = true;
            }
        } else {
            // Neither selected -> Applies to company
            $is_applicable = true;
        }

        if (!$is_applicable) {
            continue; // Skip this title if it doesn't apply to the staff member
        }
    }

    // TOTAL RESPONSE
    $responseQry = $pdo->prepare("SELECT COUNT(DISTINCT insert_login_id) total_response
        FROM {$c['answer_table']}
        WHERE {$c['id_column']} = ?");
    $responseQry->execute([$title_id]);
    $response = $responseQry->fetch(PDO::FETCH_ASSOC);

    // TOTAL STAFF (Calculating target audience based on latest active occupation record per staff)
    $staffQueryStr = "SELECT COUNT(DISTINCT latest_oi.staff_profile_id) total_staff
        FROM (
            SELECT staff_profile_id, branch_id, department, company_id,
                   ROW_NUMBER() OVER (PARTITION BY staff_profile_id ORDER BY effective_from DESC, id DESC) as rn
            FROM occupation_info
        ) latest_oi
        WHERE latest_oi.rn = 1 AND latest_oi.company_id = ?";
    $params = [$company_id ?? 0];

    if ($has_branches && $has_depts) {
        $staffQueryStr .= " AND latest_oi.branch_id IN (" . implode(',', array_fill(0, count($mapped_branches), '?')) . ")";
        $staffQueryStr .= " AND latest_oi.department IN (" . implode(',', array_fill(0, count($mapped_depts), '?')) . ")";
        $params = array_merge($params, $mapped_branches, $mapped_depts);
    } elseif ($has_branches && !$has_depts) {
        $staffQueryStr .= " AND latest_oi.branch_id IN (" . implode(',', array_fill(0, count($mapped_branches), '?')) . ")";
        $params = array_merge($params, $mapped_branches);
    } elseif (!$has_branches && $has_depts) {
        $staffQueryStr .= " AND latest_oi.department IN (" . implode(',', array_fill(0, count($mapped_depts), '?')) . ")";
        $params = array_merge($params, $mapped_depts);
    }
    $staffQry = $pdo->prepare($staffQueryStr);
    $staffQry->execute($params);
    $staff = $staffQry->fetch(PDO::FETCH_ASSOC);

    // POLL TOP ANSWER
    $top_answer = '';
    if ($type == 'poll') {
        $pollQry = $pdo->prepare("SELECT
                po.poll_options AS answer,
                COUNT(pa.poll_value) AS answer_count
            FROM {$c['answer_table']} pa
            INNER JOIN poll_options_mapping po
                ON po.id = pa.poll_value
            WHERE pa.{$c['id_column']} = ?
            GROUP BY pa.poll_value
            ORDER BY answer_count DESC");
        $pollQry->execute([$title_id]);
        $pollResults = $pollQry->fetchAll(PDO::FETCH_ASSOC);

        if ($pollResults) {
            $maxCount = $pollResults[0]['answer_count'];
            $winner = [];
            foreach ($pollResults as $poll) {
                if ($poll['answer_count'] == $maxCount) {
                    $winner[] = $poll['answer'] . " (" . $poll['answer_count'] . ")";
                }
            }
            $top_answer = implode(', ', $winner);
        }
    }

    // RATING AVERAGE
    $average_rating = '';
    if ($type == 'rating') {
        $ratingQry = $pdo->prepare("SELECT ROUND(AVG(rating_value), 2) AS avg_rating
            FROM {$c['answer_table']}
            WHERE {$c['id_column']} = ?");
        $ratingQry->execute([$title_id]);
        $ratingData = $ratingQry->fetch(PDO::FETCH_ASSOC);
        if ($ratingData['avg_rating'] !== null) {
            $average_rating = $ratingData['avg_rating'];
        }
    }

    $result_arr[] = [
        'sno'            => ++$i,
        'id'             => $row['id'],
        'title'          => $row['title'],
        'start_date'     => date('d-m-Y h:i A', strtotime($row['start_date_time'])),
        'end_date'       => date('d-m-Y h:i A', strtotime($row['end_date_time'])),
        'total_staff'    => $staff['total_staff'] ?? 0,
        'total_response' => $response['total_response'] ?? 0,
        'pending'        => ($staff['total_staff'] ?? 0) - ($response['total_response'] ?? 0),
        'top_answer'     => $top_answer,
        'average_rating' => $average_rating
    ];
}

echo json_encode($result_arr);
$pdo = null;
?>