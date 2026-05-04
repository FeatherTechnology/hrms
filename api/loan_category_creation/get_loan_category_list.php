<?php
require "../../ajaxconfig.php";

$loan_cat_arr = array();
$qry = $pdo->query("SELECT id,loan_category FROM loan_category");
if ($qry->rowCount() > 0) {
    while ($loanCategory_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanCategory_info['action'] = "<span class='icon-border_color loancatActionBtn' value='" . $loanCategory_info['id'] . "'></span>  <span class='icon-trash-2 loancatDeleteBtn' value='" . $loanCategory_info['id'] . "'></span>";
        $loan_cat_arr[] = $loanCategory_info;
    }
}

$pdo = null; //Connection Close.

echo json_encode($loan_cat_arr);
