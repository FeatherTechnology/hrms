<?php

require '../../ajaxconfig.php';

$poll_titles_id = $_POST['poll_titles_id'];

$poll_options_arr = array();

$qry = $pdo->query("SELECT id, poll_options FROM poll_options_mapping WHERE poll_titles_id = '$poll_titles_id'");

$i = 1;

while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $row['sno'] = $i;

    $poll_options_arr[] = $row;

    $i++;
}

echo json_encode($poll_options_arr);
