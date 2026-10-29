
<?php
require '../../ajaxconfig.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$type = $_POST['type'] ?? '';

if (!$id || $id < 1 || !in_array($type, ['poll', 'rating'], true)) {
   echo json_encode(['title' => '', 'options' => [], 'data' => []]);
    exit;
}

try {
    $config = [
        'poll' => [
            'title_table' => 'poll_titles',
            'title_column' => 'poll_title',
            'id_column' => 'poll_titles_id',
            'mapping_table' => 'poll_department_mapping',
            'branch_table' => 'poll_branch_mapping'
        ],
        'rating' => [
            'title_table' => 'rating_titles',
            'title_column' => 'rating_title',
            'id_column' => 'rating_titles_id',
            'mapping_table' => 'rating_department_mapping',
            'branch_table' => 'rating_branch_mapping'
        ]
    ];

    $c = $config[$type];

    $titleQry = $pdo->prepare("
        SELECT id, company_id, {$c['title_column']} AS title
        FROM {$c['title_table']}
        WHERE id = ?
    ");
    $titleQry->execute([$id]);
    $titleData = $titleQry->fetch(PDO::FETCH_ASSOC);

    if (!$titleData) {
        echo json_encode(['options' => [], 'data' => []]);
        exit;
    }

    $company_id = $titleData['company_id'];

    $branchQry = $pdo->prepare("
        SELECT branch_id
        FROM {$c['branch_table']}
        WHERE {$c['id_column']} = ?
    ");
    $branchQry->execute([$id]);
    $branches = $branchQry->fetchAll(PDO::FETCH_COLUMN);

    $deptQry = $pdo->prepare("
        SELECT department_id
        FROM {$c['mapping_table']}
        WHERE {$c['id_column']} = ?
    ");
    $deptQry->execute([$id]);
    $departments = $deptQry->fetchAll(PDO::FETCH_COLUMN);

    // Get all applicable staff using their latest occupation
    $staffSql = "
        SELECT
            u.id AS user_id,
            oi.staff_profile_id,
            sc.staff_name
        FROM (
            SELECT staff_profile_id, branch_id, department, company_id
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
            ) ranked
            WHERE rn = 1
        ) oi
        LEFT JOIN users u
            ON u.staff_name_id = oi.staff_profile_id
        LEFT JOIN staff_creation sc
            ON sc.id = oi.staff_profile_id
        WHERE oi.company_id = ?
    ";

    $staffParams = [$company_id];

    if (count($branches) > 0) {
        $staffSql .= " AND oi.branch_id IN (" .
            implode(',', array_fill(0, count($branches), '?')) . ")";
        $staffParams = array_merge($staffParams, $branches);
    }

    if (count($departments) > 0) {
        $staffSql .= " AND oi.department IN (" .
            implode(',', array_fill(0, count($departments), '?')) . ")";
        $staffParams = array_merge($staffParams, $departments);
    }

    $staffSql .= " ORDER BY sc.staff_name ASC";

    $staffQry = $pdo->prepare($staffSql);
    $staffQry->execute($staffParams);
    $staffList = $staffQry->fetchAll(PDO::FETCH_ASSOC);

    $options = [];
    $answerMap = [];

    if ($type === 'poll') {
        // Fetch all options for this poll
        $optionQry = $pdo->prepare("
            SELECT id, poll_options
            FROM poll_options_mapping
            WHERE poll_titles_id = ?
            ORDER BY id ASC
        ");
        $optionQry->execute([$id]);
        $options = $optionQry->fetchAll(PDO::FETCH_ASSOC);

        // Fetch staff selections
        $answerQry = $pdo->prepare("
            SELECT insert_login_id AS user_id, poll_value
            FROM poll_answers
            WHERE poll_titles_id = ?
            ORDER BY id ASC
        ");
        $answerQry->execute([$id]);
        $answerRows = $answerQry->fetchAll(PDO::FETCH_ASSOC);

        foreach ($answerRows as $answerRow) {
            $uid = $answerRow['user_id'];

            $selectedIds = array_filter(
                array_map(
                    'trim',
                    explode(',', (string) $answerRow['poll_value'])
                ),
                'strlen'
            );

            foreach ($selectedIds as $optionId) {
                if (ctype_digit($optionId)) {
                    $answerMap[$uid][] = $optionId;
                }
            }
        }

        foreach ($answerMap as $uid => $selectedIds) {
            $answerMap[$uid] = array_values(array_unique($selectedIds));
        }
    } else {
        // Fetch rating answers
        $answerQry = $pdo->prepare("
            SELECT insert_login_id AS user_id, rating_value AS answer
            FROM rating_answers
            WHERE rating_titles_id = ?
            ORDER BY id ASC
        ");
        $answerQry->execute([$id]);
        $answerRows = $answerQry->fetchAll(PDO::FETCH_ASSOC);

        foreach ($answerRows as $answerRow) {
            $answerMap[$answerRow['user_id']] = $answerRow['answer'] ?? '';
        }
    }

    $data = [];

    foreach ($staffList as $staff) {
        $uid = $staff['user_id'];

        if ($type === 'poll') {
            $data[] = [
                'user_id' => $uid,
                'staff_name' => $staff['staff_name'] ?? '-',
                'selected_options' => ($uid !== null)
                    ? ($answerMap[$uid] ?? [])
                    : []
            ];
        } else {
            $answer = ($uid !== null)
                ? ($answerMap[$uid] ?? '')
                : '';

            $data[] = [
                'user_id' => $uid,
                'staff_name' => $staff['staff_name'] ?? '-',
                'title' => $titleData['title'],
                'answer' => ($answer !== null && trim((string) $answer) !== '')
                    ? $answer
                    : '-'
            ];
        }
    }

echo json_encode([
    'title' => $titleData['title'],
    'options' => $options,
    'data' => $data
], JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Throwable $e) {
    error_log('Poll/rating matrix error: ' . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        'options' => [],
        'data' => [],
        'error' => 'Failed to load responses'
    ]);
}

$pdo = null;
?>
