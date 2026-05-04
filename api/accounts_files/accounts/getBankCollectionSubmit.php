<?php
session_start();
include('../../../ajaxconfig.php');

$op_date = date('Y-m-d', strtotime($_POST['op_date']));

// ----------------------- Step 1: Check accounts_collect_entry -----------------------//

$checkMain = $pdo->query("SELECT id FROM accounts_collect_entry WHERE DATE(created_on) = '$op_date'");

if ($checkMain->rowCount() > 0) {

    // accounts_collect_entry HAS RECORDS → only bank validation
    $banks = $pdo->query("SELECT bank_id FROM collection WHERE DATE(coll_date) <= '$op_date'");
    $bank_ids = $banks->fetchAll(PDO::FETCH_COLUMN, 0);

    $submitted = 0;

    foreach ($bank_ids as $bank_id) {

        $qry = $pdo->query("SELECT id FROM accounts_collect_entry WHERE bank_id = '$bank_id' AND DATE(created_on) = '$op_date'");

        if ($qry->rowCount() > 0) {
            $submitted++;
        }
    }

    echo ($submitted > 0) ? 0 : 1;

    exit;
}

// ----------------------- Step 2: NO accounts_collect_entry → check collection table ----------------------- //

$checkFallback = $pdo->query("SELECT COUNT(*) AS cnt FROM collection WHERE DATE(coll_date) <= '$op_date' AND coll_mode != 1");

$count = $checkFallback->fetch()['cnt'];

echo ($count > 0) ? 1 : 0;

$pdo = null;
