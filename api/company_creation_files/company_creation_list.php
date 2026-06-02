<?php

/** Company List **
 * Purpose:
 * - Fetches all active companies.
 * - Retrieves company details along with district name.
 * - Adds action button for editing/viewing records.
 * - Returns company data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_list_arr = [];

$stmt = $pdo->prepare("SELECT
        cc.id,
        cc.company_name,
        cc.place,
        dt.district_name,
        cc.mobile
    FROM company_creation cc
    LEFT JOIN districts dt
        ON cc.district = dt.id
    WHERE cc.status = ?
");

$stmt->execute([1]);

if ($stmt->rowCount() > 0) {

    while ($companyInfo = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $companyInfo['action'] = "
            <span class='icon-border_color companyActionBtn' value='" . $companyInfo['id'] . "'></span>
        ";

        $company_list_arr[] = $companyInfo;
    }
}

$pdo = null; // Close Connection

echo json_encode($company_list_arr);
