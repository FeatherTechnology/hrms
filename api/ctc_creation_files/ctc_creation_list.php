<?php

/** CTC Component List **
 * Purpose:
 * - Fetches all active CTC components for the selected company.
 * - Converts classification, category, and pay frequency codes to display values.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns CTC component data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];

$ctc_list_arr = [];

$i = 0;

$stmt = $pdo->prepare("SELECT *
    FROM ctc_creation
    WHERE company_id = ? 
");

$stmt->execute([$company_id ]);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Component Classification
        if ($row['component_classification'] == 1) {
            $row['component_classification'] = 'CTC';
        } elseif ($row['component_classification'] == 2) {
            $row['component_classification'] = 'NON CTC';
        }

        // Component Category
        if ($row['component_category'] == 1) {
            $row['component_category'] = 'Salary';
        } elseif ($row['component_category'] == 2) {
            $row['component_category'] = 'Reimbursement';
        }

        // Pay Frequency
        if ($row['pay_frequency'] == 1) {
            $row['pay_frequency'] = 'Per Month';
        } elseif ($row['pay_frequency'] == 2) {
            $row['pay_frequency'] = 'Per Day';
        }
         // Effective From
        if (!empty($row['effective_from'])) {
            $row['effective_from'] = date('d-m-Y', strtotime($row['effective_from']));
        }
        // Status
        if ($row['status'] == 0) {
            $row['status'] = 'Active';
        } elseif ($row['status'] == 1) {
            $row['status'] = 'In-Active';
        }

        // Action Button
        $row['action'] = "
            <span class='icon-border_color ctcActionBtn' value='" . $row['id'] . "'></span>
            &nbsp;&nbsp;&nbsp;
        ";

        $ctc_list_arr[$i] = $row;

        $i++;
    }
}

$pdo = null; // Close Connection

echo json_encode($ctc_list_arr);
