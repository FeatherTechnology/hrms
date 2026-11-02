<?php
require '../../ajaxconfig.php';
@session_start();

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? 0;
$type = $_POST['type'] ?? '';

$result_arr = [];
$i = 0;

$config = [
    'feedback' => [
        'title_table' => 'feedback_titles',
        'mapping_table' => 'feedback_department_mapping',
        'branch_table' => 'feedback_branch_mapping',
        'answer_table' => 'staff_sch_feedback',
        'id_column' => 'feedback_titles_id',
        'title_column' => 'feedback_title',
        'status_column' => 'feedback_status'
    ],
    'rating' => [
        'title_table' => 'rating_titles',
        'mapping_table' => 'rating_department_mapping',
        'branch_table' => 'rating_branch_mapping',
        'answer_table' => 'rating_answers',
        'id_column' => 'rating_titles_id',
        'title_column' => 'rating_title',
        'status_column' => 'rating_status'
    ],
    'poll' => [
        'title_table' => 'poll_titles',
        'mapping_table' => 'poll_department_mapping',
        'branch_table' => 'poll_branch_mapping',
        'answer_table' => 'poll_answers',
        'id_column' => 'poll_titles_id',
        'title_column' => 'poll_title',
        'status_column' => 'poll_status'
    ]
];

$selection_type_arr = [
    1 => 'Single Selection',
    2 => 'Multiple Selection'
];

if (!isset($config[$type])) {
    echo json_encode([]);
    exit;
}

$c = $config[$type];

$userQry = $pdo->prepare("
    SELECT user_type, director_company, staff_name_id
    FROM users
    WHERE id = ?
");
$userQry->execute([$user_id]);
$userData = $userQry->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo json_encode([]);
    exit;
}

$user_type = $userData['user_type'];
$director_company = $userData['director_company'];
$staff_name_id = $userData['staff_name_id'];

$staff_branch_id = '';
$staff_dept_id = '';
$company_id = 0;

if ($user_type == 1) {
    $company_ids = array_filter(
        array_map('intval', explode(',', (string)$director_company)),
        fn($id) => $id > 0
    );

    if (empty($company_ids)) {
        echo json_encode([]);
        exit;
    }

    $company_condition = "tt.company_id IN (" . implode(',', $company_ids) . ")";
} else {
    $deptQry = $pdo->prepare("
        SELECT department, branch_id, company_id
        FROM occupation_info
        WHERE staff_profile_id = ?
        ORDER BY effective_from DESC, id DESC
        LIMIT 1
    ");
    $deptQry->execute([$staff_name_id]);
    $deptData = $deptQry->fetch(PDO::FETCH_ASSOC);

    if (!$deptData) {
        echo json_encode([]);
        exit;
    }

    $company_id = $deptData['company_id'];
    $staff_branch_id = $deptData['branch_id'];
    $staff_dept_id = $deptData['department'];

    $company_condition = "tt.company_id = " . (int)$company_id;
}

$selection_type_column = ($type === 'poll') ? ', tt.selection_type' : '';

$titleSql = "
    SELECT
        tt.id,
        tt.company_id,
        cc.company_name,
        tt.{$c['title_column']} AS title,
        tt.start_date_time,
        tt.end_date_time
        $selection_type_column,
        (
            SELECT GROUP_CONCAT(
                DISTINCT dc.department_name
                ORDER BY dc.department_name
                SEPARATOR ', '
            )
            FROM {$c['mapping_table']} dm
            LEFT JOIN department_creation dc
                ON dc.id = dm.department_id
            WHERE dm.{$c['id_column']} = tt.id
        ) AS department_name,
        (
            SELECT GROUP_CONCAT(
                DISTINCT bc.branch_name
                ORDER BY bc.branch_name
                SEPARATOR ', '
            )
            FROM {$c['branch_table']} bm
            LEFT JOIN branch_creation bc
                ON bc.id = bm.branch_id
            WHERE bm.{$c['id_column']} = tt.id
        ) AS branch_name
    FROM {$c['title_table']} tt
    LEFT JOIN company_creation cc
        ON cc.id = tt.company_id
    WHERE
        $company_condition
        AND tt.{$c['status_column']} = 0
        AND NOW() <= tt.end_date_time
    ORDER BY tt.id DESC
";

$qry = $pdo->query($titleSql);

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
    $title_id = $row['id'];

    $branchCheckQry = $pdo->prepare("
        SELECT branch_id
        FROM {$c['branch_table']}
        WHERE {$c['id_column']} = ?
    ");
    $branchCheckQry->execute([$title_id]);
    $mapped_branches = $branchCheckQry->fetchAll(PDO::FETCH_COLUMN);

    $deptCheckQry = $pdo->prepare("
        SELECT department_id
        FROM {$c['mapping_table']}
        WHERE {$c['id_column']} = ?
    ");
    $deptCheckQry->execute([$title_id]);
    $mapped_depts = $deptCheckQry->fetchAll(PDO::FETCH_COLUMN);

    $has_branches = count($mapped_branches) > 0;
    $has_depts = count($mapped_depts) > 0;

    if ($user_type != 1) {
        $is_applicable = false;

        if ($has_branches && $has_depts) {
            $is_applicable =
                in_array($staff_branch_id, $mapped_branches) &&
                in_array($staff_dept_id, $mapped_depts);
        } elseif ($has_branches) {
            $is_applicable = in_array($staff_branch_id, $mapped_branches);
        } elseif ($has_depts) {
            $is_applicable = in_array($staff_dept_id, $mapped_depts);
        } else {
            $is_applicable = true;
        }

        if (!$is_applicable) {
            continue;
        }
    }

    $responseQry = $pdo->prepare("
        SELECT COUNT(DISTINCT insert_login_id) AS total_response
        FROM {$c['answer_table']}
        WHERE {$c['id_column']} = ?
    ");
    $responseQry->execute([$title_id]);
    $response = $responseQry->fetch(PDO::FETCH_ASSOC);

    $staffQueryStr = "
        SELECT COUNT(DISTINCT latest_oi.staff_profile_id) AS total_staff
        FROM (
            SELECT
                staff_profile_id,
                branch_id,
                department,
                company_id,
                ROW_NUMBER() OVER (
                    PARTITION BY staff_profile_id
                    ORDER BY effective_from DESC, id DESC
                ) AS rn
            FROM occupation_info
        ) latest_oi
        WHERE latest_oi.rn = 1
          AND latest_oi.company_id = ?
    ";

    $params = [$row['company_id']];

    if ($has_branches && $has_depts) {
        $staffQueryStr .= " AND latest_oi.branch_id IN (" .
            implode(',', array_fill(0, count($mapped_branches), '?')) . ")";
        $staffQueryStr .= " AND latest_oi.department IN (" .
            implode(',', array_fill(0, count($mapped_depts), '?')) . ")";
        $params = array_merge($params, $mapped_branches, $mapped_depts);
    } elseif ($has_branches) {
        $staffQueryStr .= " AND latest_oi.branch_id IN (" .
            implode(',', array_fill(0, count($mapped_branches), '?')) . ")";
        $params = array_merge($params, $mapped_branches);
    } elseif ($has_depts) {
        $staffQueryStr .= " AND latest_oi.department IN (" .
            implode(',', array_fill(0, count($mapped_depts), '?')) . ")";
        $params = array_merge($params, $mapped_depts);
    }

    $staffQry = $pdo->prepare($staffQueryStr);
    $staffQry->execute($params);
    $staff = $staffQry->fetch(PDO::FETCH_ASSOC);

    $top_answer = '';

    if ($type === 'poll') {
        $pollQry = $pdo->prepare("
            SELECT
                po.poll_options AS answer,
                COUNT(DISTINCT pa.insert_login_id) AS answer_count
            FROM poll_answers pa
            INNER JOIN poll_options_mapping po
                ON FIND_IN_SET(po.id, pa.poll_value) > 0
            WHERE pa.poll_titles_id = ?
            GROUP BY po.id, po.poll_options
            ORDER BY answer_count DESC
        ");
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

    $average_rating = '';

    if ($type === 'rating') {
        $ratingQry = $pdo->prepare("
            SELECT ROUND(AVG(rating_value), 2) AS avg_rating
            FROM rating_answers
            WHERE rating_titles_id = ?
        ");
        $ratingQry->execute([$title_id]);
        $ratingData = $ratingQry->fetch(PDO::FETCH_ASSOC);

        if ($ratingData['avg_rating'] !== null) {
            $average_rating = $ratingData['avg_rating'];
        }
    }

    $total_staff = (int)($staff['total_staff'] ?? 0);
    $total_response = (int)($response['total_response'] ?? 0);

    $result = [
        'sno' => ++$i,
        'id' => $row['id'],
        'company_name' => $row['company_name'] ?? '-',
        'branch_name' => $row['branch_name'] ?: '-',
        'department_name' => $row['department_name'] ?: '-',
        'title' => $row['title'],
        'start_date' => date('d-m-Y h:i A', strtotime($row['start_date_time'])),
        'end_date' => date('d-m-Y h:i A', strtotime($row['end_date_time'])),
        'total_staff' => $total_staff,
        'total_response' => $total_response,
        'pending' => max(0, $total_staff - $total_response),
        'top_answer' => $top_answer,
        'average_rating' => $average_rating
    ];

    if ($type === 'poll') {
        $result['selection_type'] =
            $selection_type_arr[$row['selection_type'] ?? 0] ?? '-';
    }

    $result_arr[] = $result;
}

echo json_encode($result_arr, JSON_INVALID_UTF8_SUBSTITUTE);
$pdo = null;
?>