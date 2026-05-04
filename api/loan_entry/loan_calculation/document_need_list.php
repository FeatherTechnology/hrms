<?php
require "../../../ajaxconfig.php";

$doc_need_arr = array();
$cusProfileId = $_POST['cusProfileId'];
$qry = $pdo->query("SELECT id,document_name FROM document_need where cus_profile_id = '$cusProfileId' ");
if ($qry->rowCount() > 0) {
    while ($DocNeed_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $DocNeed_info['action'] = "<span class='icon-trash-2 docNeedDeleteBtn' value='" . $DocNeed_info['id'] . "'></span>";
        $doc_need_arr[] = $DocNeed_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($doc_need_arr);