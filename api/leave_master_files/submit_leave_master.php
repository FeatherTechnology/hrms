<?php

require '../../ajaxconfig.php';

@session_start();

$company_name = $_POST['company_name'];
$week_off     = $_POST['week_off'];

$user_id = $_SESSION['user_id'];

$result = 0;

try {

    /*--- DELETE OLD WEEKOFFS ----*/
    $qry = $pdo->query("  DELETE FROM company_weekoffs  WHERE company_id = '$company_name'");

    if (!$qry) {
        echo json_encode(0);
        exit;
    }

    $qry = true;
    foreach ($week_off as $day => $week) {
        if ($week != '') {
            $qry = $pdo->query(" INSERT INTO company_weekoffs  ( company_id,  week_day,  week_off )
                VALUES ( '$company_name',  '$day', '$week') ");
            if (!$qry) {
                break;
            }
        }
    }

    if ($qry) {
        $result = 1;
    }

} catch (PDOException $e) {

    $result = 0;
}

echo json_encode($result);
?>