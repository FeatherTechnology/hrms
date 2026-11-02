
<?php
require '../../ajaxconfig.php';
@session_start();

$feedback_name = $_POST['feedback_name'] ?? '';
$commants = $_POST['commants'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$user_name = $_POST['user_name'] ?? '';

$uploaded_files = [];
$path = "../../uploads/general_feedback/";

if (!is_dir($path) && !mkdir($path, 0775, true)) {
    echo json_encode(2);
    exit;
}

$allowed_extensions = [
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'pdf', 'doc', 'docx',
    'mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma', 'opus'
];

if (isset($_FILES['attachment']['name']) && is_array($_FILES['attachment']['name'])) {

    foreach ($_FILES['attachment']['name'] as $key => $original_name) {

        if ($_FILES['attachment']['error'][$key] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($_FILES['attachment']['error'][$key] !== UPLOAD_ERR_OK) {
            echo json_encode(2);
            exit;
        }

        $tmp_name = $_FILES['attachment']['tmp_name'][$key];
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (
            !in_array($extension, $allowed_extensions, true) ||
            !is_uploaded_file($tmp_name)
        ) {
            echo json_encode(2);
            exit;
        }

        do {
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $path . $filename;
        } while (file_exists($destination));

        if (!move_uploaded_file($tmp_name, $destination)) {
            echo json_encode(2);
            exit;
        }

        $uploaded_files[] = $filename;
    }
}

// Save filenames as comma-separated values
$attachment = implode(',', $uploaded_files);

try {
    $stmt = $pdo->prepare("
        INSERT INTO staff_general_feedback
        (
            general_feedback_id,
            commants,
            user_id,
            attachment,
            insert_login_id,
            created_date
        )
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $result = $stmt->execute([
        $feedback_name,
        $commants,
        $user_name,
        $attachment,
        $user_id
    ]);

    echo json_encode($result ? 1 : 2);

} catch (Throwable $e) {
    echo json_encode(2);
}
?>
