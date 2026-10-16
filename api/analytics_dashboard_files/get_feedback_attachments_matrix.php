<?php
require '../../ajaxconfig.php';
@session_start();

$feedback_titles_id = $_POST['feedback_titles_id'] ?? '';

if (!$feedback_titles_id) {
    echo json_encode(['questions' => [], 'data' => []]);
    exit;
}

// 1. Fetch all questions configured for this feedback title
$qQry = $pdo->prepare("
    SELECT id, feedback_questions 
    FROM feedback_questions_mapping 
    WHERE feedback_titles_id = ?
");
$qQry->execute([$feedback_titles_id]);
$questions = $qQry->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch distinct staff who submitted answers along with staff names from staff_profile table
$staffQry = $pdo->prepare("
    SELECT DISTINCT 
        sf.insert_login_id AS user_id,
        sp.staff_name AS staff_name
    FROM staff_sch_feedback sf
    LEFT JOIN users u ON u.id = sf.insert_login_id
    LEFT JOIN staff_creation sp ON sp.id = u.staff_name_id
    WHERE sf.feedback_titles_id = ?
");
$staffQry->execute([$feedback_titles_id]);
$staffList = $staffQry->fetchAll(PDO::FETCH_ASSOC);

$data = [];

foreach ($staffList as $staff) {
    $user_id = $staff['user_id'];
    
    // Fetch answers/attachments map for this specific user and feedback title
    $ansQry = $pdo->prepare("
        SELECT feedback_ques_map_id, attachment 
        FROM staff_sch_feedback 
        WHERE feedback_titles_id = ? AND insert_login_id = ?
    ");
    $ansQry->execute([$feedback_titles_id, $user_id]);
    $answers = $ansQry->fetchAll(PDO::FETCH_ASSOC);

    $attachmentsMap = [];
    foreach ($ansQry as $ans) {} // Safe iteration or use fetchAll key-value pairs
    
    $attachment_arr = [];
    foreach($answers as $row) {
        $attachment_arr[$row['feedback_ques_map_id']] = $row['attachment'];
    }

    $data[] = [
        'staff_name' => $staff['staff_name'],
        'attachments' => $attachment_arr
    ];
}

echo json_encode([
    'questions' => $questions,
    'data' => $data
]);
$pdo = null;
?>