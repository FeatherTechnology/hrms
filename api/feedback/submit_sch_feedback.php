<?php
// Save feedback answers and optional file attachments submitted by the logged-in user.

require '../../ajaxconfig.php';
@session_start();

$user_id =$_SESSION['user_id'];
$feedback_configuration_id =$_POST['feedback_configuration_id'];
$answerArr =$_POST['answerArr'];

// Directory where files will be uploaded (make sure this folder exists)
$upload_dir = '../../uploads/schedule_feedback/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

foreach($answerArr as$index => $row){$question_id = $row['question_id'];$answer = $row['answer'];$attachment_name = '';

    // Check if a file was uploaded for this question
    if (isset($_FILES['answerArr']['name'][$index]['attachment']) && $_FILES['answerArr']['error'][$index]['attachment'] == 0) {
        $file_tmp =$_FILES['answerArr']['tmp_name'][$index]['attachment'];$original_name = $_FILES['answerArr']['name'][$index]['attachment'];
        
        // Generate a unique safe filename to avoid overwriting conflicts
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $attachment_name = time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        
        move_uploaded_file($file_tmp, $upload_dir .$attachment_name);
    }

    // Insert into database (Make sure your `staff_sch_feedback` table has an `attachment` column)
    $stmt =$pdo->prepare("
        INSERT INTO staff_sch_feedback (
            feedback_titles_id,
            feedback_ques_map_id,
            answer,
            attachment,
            insert_login_id
        ) VALUES (
            ?, ?, ?, ?, ?
        )
    ");
    
    $stmt->execute([$feedback_configuration_id, 
        $question_id,$answer,
        $attachment_name,$user_id
    ]);
}

echo 1;
?>