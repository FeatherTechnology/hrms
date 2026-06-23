<?php

/** Designation List **
 * Purpose:
 * - Fetches all active designations.
 * - Adds Edit and Delete action buttons for each record.
 * - Returns designation data in JSON format for DataTable/Grid display.
 */

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'] ?? '';

$designation_list_arr = [];
$i = 0;

$sql = "
    SELECT dc.*
    FROM designation_creation dc
    WHERE dc.designation_status = 0
    AND (
        (
            ? = ''
            AND dc.id NOT IN (
                SELECT designation_id
                FROM company_designation_mapping
            )
        )
        OR
        (
            ? != ''
            AND (
                dc.id IN (
                    SELECT designation_id
                    FROM company_designation_mapping
                    WHERE company_id = ?
                )
                OR dc.id NOT IN (
                    SELECT designation_id
                    FROM company_designation_mapping
                )
            )
        )
    )
    ORDER BY dc.designation
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id, $company_id, $company_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $row['sno'] = ++$i;

    $row['action'] = "
        <span class='icon-border_color designationActionBtn' value='{$row['id']}'></span>
        &nbsp;&nbsp;&nbsp;
        <span class='icon-delete designationDeleteBtn' value='{$row['id']}'></span>
    ";

    $designation_list_arr[] = $row;
}

$pdo = null;
echo json_encode($designation_list_arr);