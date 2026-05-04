<?php
require "../../../ajaxconfig.php";

$docName = $_POST['docName'];
$cusProfileId = $_POST['cusProfileId'];
$cus_id = $_POST['cusID'];

$result = 0; //Initial.
$qry = $pdo->query("SELECT * FROM `document_need` WHERE REPLACE(TRIM(document_name), ' ', '') = REPLACE(TRIM('$docName'), ' ', '') AND cus_profile_id = '$cusProfileId' ");
if ($qry->rowCount() > 0) {
    $result = 1; //already Exists.

} else {
    $pdo->query("INSERT INTO `document_need`(`cus_profile_id`, `cus_id`, `document_name`) VALUES ('$cusProfileId', '$cus_id', '$docName')");
    if ($qry) {
        $result = 2; //Insert
    }
}

echo json_encode($result);
