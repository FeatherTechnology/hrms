<?php

require '../../ajaxconfig.php';

@session_start();

$company_name   = $_POST['company_name'];
$max_permission = $_POST['max_permission'];
$week_off       = $_POST['week_off'];

$user_id        = $_SESSION['user_id'];

$result = 0;

/*-------------------------------------------------------------------- CHECK COMPANY ALREADY EXISTS ----------------------------------------------------------------- */

$checkQry = $pdo->query("SELECT id FROM company_policies WHERE company_id = '$company_name'");

$checkCount = $checkQry->rowCount();

if ($checkCount > 0) {

    /*---------------------------------------------------------------------------- UPDATE -------------------------------------------------------------------- */

    $row = $checkQry->fetch();

    $company_policies_id = $row['id'];

    $qry = $pdo->query("UPDATE company_policies SET max_permission = '$max_permission', update_login_id = '$user_id', updated_date = NOW() 
    WHERE company_id = '$company_name'");

    /*-------------------------------------------------------------------- DELETE OLD WEEKOFFS ----------------------------------------------------------------- */

    $pdo->query("DELETE FROM company_weekoffs WHERE company_policies_id = '$company_policies_id'");

    /*-------------------------------------------------------------------- INSERT NEW WEEKOFFS ----------------------------------------------------------------- */

    foreach ($week_off as $day => $week) {

        if ($week != '') {
            $pdo->query("INSERT INTO company_weekoffs (company_policies_id, week_day, week_off) VALUES ('$company_policies_id', '$day', '$week')");
        }
    }

    if ($qry) {
        $result = 1;
    }
} else {

    /*---------------------------------------------------------------------------- INSERT -------------------------------------------------------------------- */

    $qry = $pdo->query("INSERT INTO company_policies (company_id, max_permission, insert_login_id, created_date) VALUES ('$company_name', '$max_permission', '$user_id', NOW())");

    $company_policies_id = $pdo->lastInsertId();

    /*---------------------------------------------------------------------------- INSERT WEEKOFFS -------------------------------------------------------------- */

    foreach ($week_off as $day => $week) {

        if ($week != '') {
            $pdo->query("INSERT INTO company_weekoffs (company_policies_id, week_day, week_off) VALUES ('$company_policies_id', '$day', '$week')");
        }
    }

    if ($qry) {
        $result = 2;
    }
}

echo json_encode($result);
