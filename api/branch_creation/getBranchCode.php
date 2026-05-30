<?php
require('../../ajaxconfig.php');

$response = array();

$company_id = $_POST["company_name"] ?? '';

try {

    $qry1 = $pdo->query("SELECT company_name FROM company_creation WHERE id = '$company_id'");
    $qry_info = $qry1->fetch();
    $company_name = trim($qry_info["company_name"]);
    // Split words
    $words = preg_split('/\s+/', $company_name);
    // Generate prefix
    $prefix = '';
    if (count($words) > 1) {
        // Multiple words
        foreach ($words as $word) {
            $prefix .= strtoupper(mb_substr($word, 0, 1));
        }
    } else {
        // Single word
        $prefix = strtoupper(mb_substr($company_name, 0, 1));
    }

    // Get last branch code
    $qry = $pdo->query("SELECT MAX(branch_code) as branch_code FROM branch_creation WHERE company_id = '$company_id'");
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    if ($row["branch_code"] != '') {
        // Example: MC-101
        $ac2 = $row["branch_code"];
        $appno2 = ltrim(strstr($ac2, '-'), '-');
        $appno2 = (int)$appno2 + 1;
        $branch_code = $prefix . "-" . $appno2;
    } else {
        // Initial code
        $branch_code = $prefix . "-101";
    }

    $response['branch_code'] = $branch_code;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;
echo json_encode($response);
