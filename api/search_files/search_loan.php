<?php
require '../../ajaxconfig.php';

$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : null;
$aadhar_num = isset($_POST['aadhar_num']) ? trim($_POST['aadhar_num']) : '';
$cus_name = isset($_POST['cus_name']) ? trim($_POST['cus_name']) : '';
$area = isset($_POST['area']) ? trim($_POST['area']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';

$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : null;
$loan_list_arr = array();
$status = [
    1 => 'Loan Entry',
    2 => 'Loan Entry',
    3 => 'Loan Approval',
    4 => 'Loan Issued',
    5 => 'Loan Approval',
    6 => 'Loan Approval',
    7 => 'Present',
    8 => 'Closed',
    9 => 'Closed',
    10 => 'NOC',
    11 => 'NOC',
    12 => 'NOC',
    13 => 'Loan Issue',
    14 => 'Loan Issue'
];

$whereClause = "WHERE 1"; // Initial WHERE clause

if (!empty($cus_id)) {
    $whereClause .= " AND lelc.cus_id = '$cus_id'";
} elseif (!empty($aadhar_num)) {
    $whereClause .= " AND lelc.cus_id IN (SELECT cus_id FROM customer_profile WHERE aadhar_num LIKE '%$aadhar_num%')";
} elseif (!empty($cus_name)) {
    $whereClause .= " AND lelc.cus_id IN (SELECT cus_id FROM customer_profile WHERE cus_name LIKE '%$cus_name%')";
} elseif (!empty($area)) {
    $whereClause .= " AND lelc.cus_id IN (
        SELECT cp.cus_id
        FROM customer_profile cp
        LEFT JOIN area_name_creation anc ON cp.area = anc.id
        WHERE anc.areaname LIKE '%$area%'
    )";
} elseif (!empty($mobile)) {
    $whereClause .= " AND lelc.cus_id IN (SELECT cus_id FROM customer_profile WHERE mobile1 LIKE '%$mobile%')";
}
$qry = $pdo->query("SELECT lelc.id, lelc.cus_profile_id, lelc.cus_id, lelc.loan_date, lelc.loan_id, lc.loan_category, lelc.loan_amount, cs.status, cs.sub_status
    FROM loan_entry_loan_calculation lelc
    JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
    JOIN loan_category lc ON lcc.loan_category = lc.id
    JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
    $whereClause ORDER BY lelc.id DESC");
if ($qry->rowCount() > 0) {
    $i=1;
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $response = array();
        $loanDate = new DateTime($row['loan_date']);

        $response['loan_date'] = $loanDate->format('d-m-Y');
        $response['loan_id'] = $row['loan_id'];
        $response['loan_category'] = $row['loan_category'];
        $response['loan_amount'] = moneyFormatIndia($row['loan_amount']);
        $response['status'] = $status[$row['status']];

        // Calculate loan customer status and store in variable
        $loanCustomerStatus = loanCustomerStatus($pdo, $row['cus_profile_id'], $i);
        $response['sub_status'] = $loanCustomerStatus;

        $response['info'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'>
                <i class='fa'>&#xf107;</i>
            </button>
            <div class='dropdown-content'>";
        $response['info'] .=  "<a href='#' class='customer-profile' value='" . $row['cus_profile_id'] . "'>Customer Profile</a>";
        $response['info'] .=  "  <a href='#' class='loan-calculation' value='" . $row['id'] . "'>Loan Calculation</a>";
        $response['info'] .=  " <a href='#' class='documentation' value='" . $row['cus_profile_id'] . "'>Documentation</a>";
        if ($row['status'] >= '8' && $row['status'] != '13' && $row['status'] != '14') {
            $response['info'] .=  " <a href='#' class='closed-remark' value='" . $row['cus_profile_id'] . "'>Remark View</a>";
        }
        if ($row['status'] == '10' || $row['status'] == '11') {
            $response['info'] .=  " <a href='#' class='noc-summary' value='" . $row['cus_profile_id'] . "'>Noc Summary</a>";
        }
        $response['info'] .=  "  </div> </div>";

        if ($row['status'] < 7 || $row['status'] == 13 || $row['status'] == 14) { // Condition: Less than 7 and 13 and 14 
            $response['charts'] = "<div class='dropdown'>
                <button class='btn btn-outline-secondary' disabled>
                    <i class='fa'>&#xf107;</i>
                </button>
                <div class='dropdown-content'>
                    <a href='#' class='due-chart' value='" . $row['cus_profile_id'] . "' style='pointer-events: none; color: gray;'>Due Chart</a>
                    <a href='#' class='penalty-chart' value='" . $row['cus_profile_id'] . "' style='pointer-events: none; color: gray;'>Penalty Chart</a>
                    <a href='#' class='fine-chart' value='" . $row['cus_profile_id'] . "' style='pointer-events: none; color: gray;'>Fine Chart</a>
                </div>
            </div>";
        } else {
            $response['charts'] = "<div class='dropdown'>
                <button class='btn btn-outline-secondary'>
                    <i class='fa'>&#xf107;</i>
                </button>
                <div class='dropdown-content'>
                    <a href='#' class='due-chart' value='" . $row['cus_profile_id'] . "'>Due Chart</a>
                    <a href='#' class='penalty-chart' value='" . $row['cus_profile_id'] . "'>Penalty Chart</a>
                    <a href='#' class='fine-chart' value='" . $row['cus_profile_id'] . "'>Fine Chart</a>
                </div>
            </div>";
        }

        $loan_list_arr[] = $response;
         $i++; // move to next element for next profile
    }
}

$pdo = null; // Close Connection
echo json_encode($loan_list_arr);

function loanCustomerStatus($pdo, $cus_profile_id ,$i)
{
    $qry1 = $pdo->query("SELECT lelc.loan_date, cs.status as cs_status, cs.sub_status as sub_sts
    FROM loan_entry_loan_calculation lelc
    JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
    WHERE lelc.cus_profile_id = '$cus_profile_id' ORDER BY lelc.id DESC");

    if ($qry1->rowCount() > 0) {
        $row1 = $qry1->fetch(PDO::FETCH_ASSOC);
        $cs_status = $row1['cs_status'];
        $sub_sts = $row1['sub_sts'];
        $loan_date = $row1['loan_date'];
        $pending_sts = isset($_POST["pending_sts"]) ? explode(',', $_POST["pending_sts"]) : [];
        $od_sts = isset($_POST["od_sts"]) ? explode(',', $_POST["od_sts"]) : [];
        $due_nil_sts = isset($_POST["due_nil_sts"]) ? explode(',', $_POST["due_nil_sts"]) : [];
        $bal_amt = isset($_POST["bal_amt"]) ? explode(',', $_POST["bal_amt"]) : [];
        
        if ($cs_status == '1' || $cs_status == '2') {
            $status = 'Loan Entry';
        } elseif ($cs_status == '3') {
            $status = 'In Approval';
        } elseif ($cs_status == '4') {
            $status = 'In Loan Issue';
        } elseif ($cs_status == '5') {
            $status = 'Cancel';
        } elseif ($cs_status == '6') {
            $status = 'Revoke';
        } elseif ($cs_status == '7') {
            $curdate = date('Y-m-d');
            if (date('Y-m-d', strtotime($loan_date)) > date('Y-m-d', strtotime($curdate)) && isset($bal_amt[$i - 1]) && $bal_amt[$i - 1] != 0) {
                $status = 'Current';
            } else {
                if (isset($pending_sts[$i - 1]) && $pending_sts[$i - 1] == 'true' && isset($od_sts[$i - 1]) && $od_sts[$i - 1] == 'false') {
                    $status = 'Pending';
                } elseif (isset($od_sts[$i - 1]) && $od_sts[$i - 1] == 'true' && isset($due_nil_sts[$i - 1]) && $due_nil_sts[$i - 1] == 'false') {
                    $status = 'OD';
                } elseif (isset($due_nil_sts[$i - 1]) && $due_nil_sts[$i - 1] == 'true') {
                    $status = 'Due Nil';
                } elseif (isset($pending_sts[$i - 1]) && $pending_sts[$i - 1] == 'false') {
                    $status = 'Current';
                }
            }
        } elseif ($cs_status == '8') {
            $status = 'In Closed';
        } elseif ($cs_status == '9') {
            if ($sub_sts == '1') {
                $status = 'Consider';
            } elseif ($sub_sts == '2') {
                $status = 'Rejected';
            }
        } elseif ($cs_status == '10') {
            $status = 'Pending';
        } elseif ($cs_status == '11') {
            $status = 'Completed';
        } elseif ($cs_status == '12') {
            $status = 'Completed';
        } elseif ($cs_status == '13') {
            $status = 'Cancel';
        } elseif ($cs_status == '14') {
            $status = 'Revoke';
        }

        return $status;
    }

    return ''; // Default return value if no conditions match
}
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
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

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}
