
<?php
require '../../ajaxconfig.php';
@session_start();

header('Content-Type: text/plain');

$user_id = $_SESSION['user_id'] ?? '';
$feedback_configuration_id = $_POST['feedback_configuration_id'] ?? '';
$answerArr = $_POST['answerArr'] ?? [];

$upload_dir = '../../uploads/schedule_feedback/';
$uploaded_paths = [];

if (empty($user_id) || empty($feedback_configuration_id) || empty($answerArr) || !is_array($answerArr)) {
    echo 2;
    exit;
}

if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
    echo 2;
    exit;
}

$allowed_extensions = [
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'pdf', 'doc', 'docx',
    'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'opus'
];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO staff_sch_feedback (
            feedback_titles_id,
            feedback_ques_map_id,
            answer,
            attachment,
            insert_login_id
        ) VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($answerArr as $index => $row) {
        $question_id = $row['question_id'] ?? '';
        $answer = trim($row['answer'] ?? '');
        $attachment_names = [];

        if ($question_id === '' || $answer === '') {
            throw new Exception('Invalid feedback answer.');
        }

        if (
            isset($_FILES['answerArr']['name'][$index]['attachment']) &&
            is_array($_FILES['answerArr']['name'][$index]['attachment'])
        ) {
            $file_names = $_FILES['answerArr']['name'][$index]['attachment'];
            $file_tmp_names = $_FILES['answerArr']['tmp_name'][$index]['attachment'];
            $file_errors = $_FILES['answerArr']['error'][$index]['attachment'];

            foreach ($file_names as $file_index => $original_name) {
                $error = $file_errors[$file_index] ?? UPLOAD_ERR_NO_FILE;

                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                if ($error !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload failed.');
                }

                $tmp_name = $file_tmp_names[$file_index] ?? '';
                $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                if (
                    !in_array($extension, $allowed_extensions, true) ||
                    !is_uploaded_file($tmp_name)
                ) {
                    throw new Exception('Invalid attachment file.');
                }

                do {
                    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
                    $destination = $upload_dir . $filename;
                } while (file_exists($destination));

                if (!move_uploaded_file($tmp_name, $destination)) {
                    throw new Exception('Unable to save attachment.');
                }

                $uploaded_paths[] = $destination;
                $attachment_names[] = $filename;
            }
        }

        // Store multiple filenames separated by commas.
        $attachment = implode(',', $attachment_names);

        $stmt->execute([
            $feedback_configuration_id,
            $question_id,
            $answer,
            $attachment,
            $user_id
        ]);
    }

    $pdo->commit();
    echo 1;

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    foreach ($uploaded_paths as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    echo 2;
}
?>
