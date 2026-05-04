<?php
require '../../ajaxconfig.php';

$status = [
    8 =>  'In Closed',
    9 =>  'Closed',
    10 => 'Move to NOC',
    11 => 'NOC Completed',
    12 => 'Removed From NOC'
];

$existing_promo_arr = [];
$whereCondition = "";

/* ---------------- FILTER BLOCK ---------------- */
if (isset($_POST['existing_details']) && !empty($_POST['existing_details'])) {
    $details = $_POST['existing_details'];
    $conditions = [];

    $normalSelections = array_intersect($details, ['needed', 'later']);
    if (!empty($normalSelections)) {
        $normalSelectionsStr = implode("','", array_map('ucfirst', $normalSelections));
        $conditions[] = "replace(ec.existing_detail,' ','') IN ('$normalSelectionsStr')";
    }

    if (in_array('tofollow', $details)) {
        $conditions[] = "(ec.existing_detail IS NULL OR ec.existing_detail NOT IN ('Needed','Later'))";
    }

    if (!empty($conditions)) {
        $whereCondition = "AND (" . implode(" OR ", $conditions) . ")";
    }
}

/* ------------ MAIN QUERY (LATEST STATUS ONLY) ------------ */
$rawQry = $pdo->query("
    SELECT  
        cp.id AS cp_id, cp.cus_id, cp.aadhar_num, cp.cus_name, cp.mobile1,
        anc.areaname AS area, lnc.linename, bc.branch_name,
        cs.status AS c_sts, cs.sub_status AS c_substs,
        ec.created_on AS created, ec.existing_detail,
        cp.created_on AS cus_created

    FROM customer_profile cp

    LEFT JOIN (
        SELECT *, ROW_NUMBER() OVER (PARTITION BY cus_profile_id ORDER BY id DESC) AS rn
        FROM customer_status
    ) cs ON cs.cus_profile_id = cp.id AND cs.rn = 1

    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN area_creation ac ON cp.line = ac.line_id
    LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
    LEFT JOIN existing_customer ec ON cp.id = ec.cus_profile_id

    WHERE 1 $whereCondition
    ORDER BY cp.id DESC
");

$allRows = $rawQry->fetchAll(PDO::FETCH_ASSOC);

/* ---------------- GROUP BY AADHAAR ---------------- */
$grouped = [];
foreach ($allRows as $row) {
    $grouped[$row['aadhar_num']][] = $row;
}

$finalRows = [];

/* -------------- APPLY CONDITIONS FOR EACH AADHAAR -------------- */
foreach ($grouped as $aadhaar => $records) {

    // Sort by latest cp_id DESC
    usort($records, fn($a, $b) => $b['cp_id'] <=> $a['cp_id']);

    $count  = count($records);
    $latest = $records[0]; // Always newest record

    /* ----------- Condition 1: Only one record ----------- */
    if ($count == 1) {
        $r = $latest;

        if (
            $r['c_sts'] >= 9 &&
            !in_array($r['c_sts'], [13, 14]) &&
            $r['c_substs'] == 1
        ) {
            $finalRows[] = $r;
        }
        continue;
    }

    /* ----------- Condition 2: Two or more records ----------- */
    if ($count >= 2) {

        $valid = true;

        foreach ($records as $r) {

            // Must be >= 9
            if ($r['c_sts'] < 9) {
                $valid = false;
                break;
            }

            // 13 & 14 NOT allowed
            if (in_array($r['c_sts'], [13, 14])) {
                $valid = false;
                break;
            }
        }

        // Latest substatus must be 1
        if ($valid && $latest['c_substs'] == 1) {
            $finalRows[] = $latest;
        }
    }
}


/* ---------------- ADD STATUS & ACTION BUTTON ---------------- */
foreach ($finalRows as &$customerRow) {

    $loanCustomerStatus = loanCustomerStatus($pdo, $customerRow['cus_id']);
    $customerRow['c_substs'] = $loanCustomerStatus;
    $customerRow['c_sts'] = $status[$customerRow['c_sts']];

    if ($customerRow['existing_detail'] != '') {
        $customerRow['action'] = $customerRow['existing_detail'];
    } else {
        $customerRow['action'] = "
            <div class='dropdown'>
                <button class='btn btn-outline-secondary'>
                    <i class='fa'>&#xf107;</i>
                </button>
                <div class='dropdown-content'>
                    <a href='#' class='exs_needed' value='{$customerRow['cus_id']}' data='Needed'>Needed</a>
                    <a href='#' class='exs_later' value='{$customerRow['cus_id']}' data='Later'>Later</a>
                </div>
            </div>
        ";
    }

    $existing_promo_arr[] = $customerRow;
}

echo json_encode($existing_promo_arr);
// Function to fetch loan status of a customer
function loanCustomerStatus($pdo, $cus_id)
{
    $stmt = $pdo->prepare("
        SELECT cs.status as cs_status, cs.sub_status as sub_sts
        FROM loan_entry_loan_calculation lelc
        JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
        WHERE lelc.cus_id = :cus_id 
        ORDER BY lelc.id DESC 
        LIMIT 1
    ");

    $stmt->execute(['cus_id' => $cus_id]);
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row1) {
        $cs_status = $row1['cs_status'];
        $sub_sts = $row1['sub_sts'];

        if ($cs_status == '9') return ($sub_sts == '1') ? 'Consider' : 'Rejected';
        if ($cs_status == '10') return 'Pending';
        if ($cs_status == '11') return 'Completed';
        if ($cs_status == '12') return ($sub_sts == '1') ? 'Consider' : 'Rejected';
    }

    return '';
}

$pdo = null;
