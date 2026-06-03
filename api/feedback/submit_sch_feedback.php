<?php
// Save feedback answers submitted by the logged-in user.

require '../../ajaxconfig.php';
@session_start();

$user_id = $_SESSION['user_id'];

$feedback_configuration_id = $_POST['feedback_configuration_id'];

$answerArr = $_POST['answerArr'];

foreach($answerArr as $row){

    $question_id = $row['question_id'];
    $answer = $row['answer'];
    $pdo->query("
        INSERT INTO staff_sch_feedback (
            feedback_titles_id,
            feedback_ques_map_id,
            answer,
            insert_login_id
        ) VALUES (
            '$feedback_configuration_id', 
            '$question_id', 
            '$answer',
            '$user_id'
        )
    ");

}

echo 1;

?>