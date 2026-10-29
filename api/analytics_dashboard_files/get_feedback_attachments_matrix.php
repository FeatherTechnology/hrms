<?php
require '../../ajaxconfig.php';
session_start();
$feedback_titles_id = filter_input(INPUT_POST, 'feedback_titles_id', FILTER_VALIDATE_INT);

if (!$feedback_titles_id || $feedback_titles_id < 1) {
    echo json_encode(['questions' => [], 'data' => []]);
    exit;
}

try {
    // 1. Get feedback title and its company
    $titleQry = $pdo->prepare("
        SELECT id, company_id
        FROM feedback_titles
        WHERE id = ?
    ");
    $titleQry->execute([$feedback_titles_id]);
    $title = $titleQry->fetch(PDO::FETCH_ASSOC);

    if (!$title) {
        echo json_encode(['questions' => [], 'data' => []]);
        exit;
    }

    $company_id = $title['company_id'];

    // 2. Get questions for this feedback title
    $qQry = $pdo->prepare("
        SELECT id, feedback_questions
        FROM feedback_questions_mapping
        WHERE feedback_titles_id = ?
        ORDER BY id ASC
    ");
    $qQry->execute([$feedback_titles_id]);
    $questions = $qQry->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get mapped branches and departments
    $branchQry = $pdo->prepare("
        SELECT branch_id
        FROM feedback_branch_mapping
        WHERE feedback_titles_id = ?
    ");
    $branchQry->execute([$feedback_titles_id]);
    $branches = $branchQry->fetchAll(PDO::FETCH_COLUMN);

    $deptQry = $pdo->prepare("
        SELECT department_id
        FROM feedback_department_mapping
        WHERE feedback_titles_id = ?
    ");
    $deptQry->execute([$feedback_titles_id]);
    $departments = $deptQry->fetchAll(PDO::FETCH_COLUMN);

    // 4. Get each staff member's latest occupation
    $staffSql = "
        SELECT
            u.id AS user_id,
            sp.staff_name,
            latest_oi.branch_id,
            latest_oi.department
        FROM users u
        INNER JOIN (
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
            ) oi
            WHERE rn = 1
        ) latest_oi
            ON latest_oi.staff_profile_id = u.staff_name_id
        LEFT JOIN staff_creation sp
            ON sp.id = u.staff_name_id
        WHERE latest_oi.company_id = ?
    ";

    $staffParams = [$company_id];

    // Match the same branch/department mapping rules used by the dashboard
    if (count($branches) > 0) {
        $staffSql .= " AND latest_oi.branch_id IN (" .
            implode(',', array_fill(0, count($branches), '?')) . ")";
        $staffParams = array_merge($staffParams, $branches);
    }

    if (count($departments) > 0) {
        $staffSql .= " AND latest_oi.department IN (" .
            implode(',', array_fill(0, count($departments), '?')) . ")";
        $staffParams = array_merge($staffParams, $departments);
    }

    $staffSql .= " ORDER BY sp.staff_name ASC";

    $staffQry = $pdo->prepare($staffSql);
    $staffQry->execute($staffParams);
    $staffList = $staffQry->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch answers and attachments submitted for this title
    $answerQry = $pdo->prepare("
        SELECT
            insert_login_id AS user_id,
            feedback_ques_map_id,
            answer,
            attachment
        FROM staff_sch_feedback
        WHERE feedback_titles_id = ?
    ");
    $answerQry->execute([$feedback_titles_id]);
    $answerRows = $answerQry->fetchAll(PDO::FETCH_ASSOC);

    $answerMap = [];

    foreach ($answerRows as $answerRow) {
        $uid = $answerRow['user_id'];
        $qid = $answerRow['feedback_ques_map_id'];

        $answerMap[$uid][$qid] = [
            'answer' => $answerRow['answer'],
            'attachment' => $answerRow['attachment']
        ];
    }

    // 6. Include every applicable staff member, even with no answers
    $data = [];

    foreach ($staffList as $staff) {
        $uid = $staff['user_id'];
        $answers = [];
        $attachments = [];

        foreach ($questions as $question) {
            $qid = $question['id'];
            $answers[$qid] = $answerMap[$uid][$qid]['answer'] ?? '';
            $attachments[$qid] = $answerMap[$uid][$qid]['attachment'] ?? '';
        }

        $data[] = [
            'user_id' => $uid,
            'staff_name' => $staff['staff_name'] ?? '-',
            'answers' => $answers,
            'attachments' => $attachments
        ];
    }

    echo json_encode([
        'questions' => $questions,
        'data' => $data
    ], JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Throwable $e) {
    error_log('Feedback attachment matrix error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['questions' => [], 'data' => [], 'error' => 'Failed to load feedback data']);
}

$pdo = null;
?>