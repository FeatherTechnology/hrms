<?php
require '../../ajaxconfig.php';
@session_start();

$agent_code = $_POST['agent_code'];
$agent_name=$_POST['agent_name'];
$mobile1=$_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$area = $_POST['area'];
$occupation = $_POST['occupation'];
$user_id = $_SESSION['user_id'];
$agent_id = $_POST['agent_id'];
$result = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest Branch code
    $selectIC = $pdo->query("SELECT agent_code FROM agent_creation WHERE agent_code != '' ORDER BY id DESC LIMIT 1 FOR UPDATE");

    if ($agent_id != '') {
        $qry = $pdo->query("UPDATE `agent_creation` SET `agent_code`='$agent_code',`agent_name`='$agent_name',`mobile1`='$mobile1',`mobile2`='$mobile2',`area`='$area',`occupation`='$occupation',`update_login_id`='$user_id',updated_date = now() WHERE `id`='$agent_id'");

        if ($qry) {
            $result = 1; //Update
        }
    } else {
        $myStr = "AG";
        if ($selectIC->rowCount() > 0) {
            $row = $selectIC->fetch();
            $ac2 = $row["agent_code"];
            $appno2 = ltrim(strstr($ac2, '-'), '-');
            $appno2 = $appno2 + 1;
            $agent_code = $myStr . "-" . $appno2;
        } else {
            $initialapp = $myStr . "-101";
            $agent_code = $initialapp;
        }

        $qry = $pdo->query("INSERT INTO `agent_creation`(`agent_code`, `agent_name`,`mobile1`,`mobile2`, `area`, `occupation`, `insert_login_id`,`created_date`) VALUES ('$agent_code','$agent_name', '$mobile1','$mobile2','$area','$occupation','$user_id',now())");

        if ($qry) {
            $result = 2; //Insert.
        }
    } // Commit transaction
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}

$pdo = null; // Close Connection

echo json_encode($result);

?>