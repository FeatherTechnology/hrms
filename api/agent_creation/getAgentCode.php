<?php
require '../../ajaxconfig.php';

$myStr = "AG";


$selectIC = $pdo->query("SELECT agent_code FROM agent_creation WHERE agent_code != ''");

if ($selectIC->rowCount() > 0) {
    
    $codeAvailable = $pdo->query("SELECT agent_code FROM agent_creation WHERE agent_code != '' ORDER BY id DESC LIMIT 1");
    $row = $codeAvailable->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $ac2 = $row["agent_code"];
        $appno2 = ltrim(strstr($ac2, '-'), '-'); 
        $appno2 = $appno2 + 1;
        $agent_code = $myStr . "-" . $appno2;
    }
} else {
    $initialapp = $myStr . "-101";
    $agent_code = $initialapp;
}

echo json_encode($agent_code);
?>
