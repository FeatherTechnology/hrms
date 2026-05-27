<?php

require '../../ajaxconfig.php';

$feedback_configuration_id = $_POST['feedback_configuration_id'];

$response = array();

//////////////////////////////////////////////////////
// GET FEEDBACK TITLE
//////////////////////////////////////////////////////

$titleQry = $pdo->query("
    SELECT feedback_title
    FROM feedback_titles
    WHERE id = '$feedback_configuration_id'
");

$titleData = $titleQry->fetch(PDO::FETCH_ASSOC);

$response['feedback_title'] = $titleData['feedback_title'];

//////////////////////////////////////////////////////
// GET QUESTIONS
//////////////////////////////////////////////////////

$questions = array();

$qry = $pdo->query("
    SELECT id, feedback_questions
    FROM feedback_questions_mapping
    WHERE feedback_titles_id = '$feedback_configuration_id'
");

$i = 0;

while($row = $qry->fetch(PDO::FETCH_ASSOC)){

    $row['sno'] = $i + 1;

    $questions[] = $row;

    $i++;
}

$response['questions'] = $questions;

echo json_encode($response);

?>