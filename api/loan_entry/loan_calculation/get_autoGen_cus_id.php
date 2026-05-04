<?php
require "../../../ajaxconfig.php";
$response = array();
$id = $_POST['id'];
if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT cus_id  FROM customer_profile WHERE cus_id = '$id'");
    $qry_info = $qry->fetch();
    $auto_cus_id = $qry_info['cus_id'];
} else {

    $qry1 = $pdo->query("SELECT `company_name` FROM `company_creation` WHERE 1 ");
    $qry_info = $qry1->fetch();
    $company_name = $qry_info["company_name"];
    $str = preg_replace('/\s+/', '', $company_name);
    $myStr = mb_substr($str, 0, 1);


    $qry = $pdo->query("SELECT MAX(cus_id) as cus_id FROM customer_profile");
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    if ($row["cus_id"] !='') {
        // If branch codes exist, generate a new branch code
        $ac2 = $row["cus_id"];
        $appno2 = ltrim(strstr($ac2, '-'), '-');
        $appno2 = $appno2 + 1;
        $auto_cus_id = $myStr . "-" . $appno2;
    } else {
        // If no branch codes exist, set an initial one
        $initialapp = $myStr . "-101";
        $auto_cus_id = $initialapp;
    }
}
$response['cus_id'] = $auto_cus_id;
echo json_encode($response);
?>






