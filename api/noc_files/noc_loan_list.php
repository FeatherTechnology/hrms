<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$cus_id = $_POST['cus_id'];

function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash;
}

$sub_sts = ['' => '', 1 => 'Consider', 2 => 'Reject'];

$loan_list_arr = array();
$qry = $pdo->query("SELECT lelc.cus_profile_id as cp_id, lelc.cus_id, lelc.loan_id, lc.loan_category, li.issue_date, cs.closed_date, lelc.loan_amount, cs.sub_status
FROM loan_entry_loan_calculation lelc
JOIN customer_profile cp ON lelc.cus_profile_id = cp.id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
JOIN loan_issue li ON lelc.cus_profile_id = li.cus_profile_id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
JOIN users urs ON FIND_IN_SET(lelc.loan_category, urs.loan_category)
WHERE li.balance_amount = 0 AND lelc.cus_id = '$cus_id' AND (cs.status = 10 OR cs.status = 11) AND u.id ='$user_id' AND urs.id ='$user_id' ORDER BY lelc.id DESC ");
if ($qry->rowCount() > 0) {
    while ($loanInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanInfo['issue_date'] = date('d-m-Y', strtotime($loanInfo['issue_date']));
        $loanInfo['closed_date'] = date('d-m-Y', strtotime($loanInfo['closed_date']));
        $loanInfo['loan_amount'] = moneyFormatIndia($loanInfo['loan_amount']);
        $loanInfo['status'] = 'Closed';
        $loanInfo['sub_status'] = $sub_sts[$loanInfo['sub_status']];
        $loanInfo['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='noc-summary' value='".$loanInfo['cp_id']."' title='Edit details'>NOC Summary</a><a href='#' data-toggle='modal' data-target='#closed_remark_model' id='remark_view' value='".$loanInfo['cp_id']."' title='Edit details'>Remark View</a></div></div>";

        $loan_list_arr[] = $loanInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_list_arr);
