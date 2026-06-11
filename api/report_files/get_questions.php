<?php
require '../../ajaxconfig.php';

$response = [];

$title_id = $_POST['title_id'] ?? '';

try {

    $stmt = $pdo->prepare("SELECT
            id,
            feedback_questions
        FROM feedback_questions_mapping
        WHERE feedback_titles_id = ?
    ");

    $stmt->execute([$title_id]);

    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    $response = [
        'status' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
