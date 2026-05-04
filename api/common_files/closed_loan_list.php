<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$cus_id = $_POST['cus_id'];
$loan_list_arr = array();
$status = [3 =>'Move',4 => 'Approved',5 => 'Cancel',6 => 'Revoke',7 => 'Loan Issued',8 => 'Closed',9=>'Closed',10=>'NOC'];
$sub_status = [''=>'',1=>'Consider',2=>'Reject'];
$qry = $pdo->query("SELECT lelc.cus_profile_id, lelc.cus_id, lelc.loan_id, lc.loan_category, lelc.loan_date,cs.closed_date,lelc.loan_amount, cs.status, cs.sub_status
FROM loan_entry_loan_calculation lelc
JOIN customer_profile cp ON cp.id = lelc.cus_profile_id
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
WHERE lelc.cus_id = '$cus_id' AND u.id ='$user_id' AND us.id ='$user_id' AND (cs.status = 8 || cs.status = 9) ORDER BY lelc.id DESC");
if ($qry->rowCount() > 0) {
    while ($loanInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanDate = new DateTime($loanInfo['loan_date']);
        $loanInfo['loan_date'] = $loanDate->format('d-m-Y');
        $closedDate = new DateTime($loanInfo['closed_date']);
        $loanInfo['closed_date'] = $closedDate->format('d-m-Y');
        $loanInfo['charts'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='due-chart' value='" . $loanInfo['cus_profile_id'] . "'>Due Chart</a><a href='#' class='penalty-chart' value='" . 
        $loanInfo['cus_profile_id'] . "'>Penalty Chart</a><a href='#' class='fine-chart' value='" . $loanInfo['cus_profile_id'] . "'>Fine Chart</a></div></div>";

        $loanInfo['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
        <div class='dropdown-content'>";

        if ($loanInfo['status'] == '8') {
            
            $loanInfo['action'] .= "<a href='#' class='closed-view' value='" . $loanInfo['cus_profile_id'] . "'>Close</a>";
        }

        $loanInfo['action'] .= "</div></div>";
        $loanInfo['status'] = $status[$loanInfo['status']];
        $loanInfo['sub_status'] = $sub_status[$loanInfo['sub_status']];
        $loan_list_arr[] = $loanInfo;
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_list_arr);
