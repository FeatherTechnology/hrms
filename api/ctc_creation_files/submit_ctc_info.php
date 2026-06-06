<?php

/** CTC Component Save **
 * Purpose:
 * - Checks whether the salary component already exists.
 * - Updates an existing CTC component when ctc_id is provided.
 * - Inserts a new CTC component when ctc_id is empty.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Salary Component Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$company_id               = $_POST['company_id'];
$ctc_id                   = $_POST['ctc_id'];
$salary_component         = $_POST['salary_component'];
$component_classification = $_POST['component_classification'];
$component_category       = $_POST['component_category'];
$pay_frequency            = $_POST['pay_frequency'];
$user_id                  = $_SESSION['user_id'];

$result = 0;

/* Check Duplicate Salary Component */
$stmt = $pdo->prepare("SELECT id
    FROM ctc_creation
    WHERE REPLACE(TRIM(salary_component), ' ', '') = REPLACE(TRIM(?), ' ', '')
    AND salary_component = ?
    AND component_classification = ?
    AND component_category = ?
    AND pay_frequency = ?
    AND status = 0
    AND company_id = ?
");

$stmt->execute([
    $salary_component,
    $salary_component,
    $component_classification,
    $component_category,
    $pay_frequency,
    $company_id
]);

if ($stmt->rowCount() > 0) {

    $result = 3; // Already Exists

} else {

    if (!empty($ctc_id)) {

        /* Update CTC Component */
        $stmt = $pdo->prepare("UPDATE ctc_creation
            SET
                company_id = ?,
                salary_component = ?,
                component_classification = ?,
                component_category = ?,
                pay_frequency = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $company_id,
            $salary_component,
            $component_classification,
            $component_category,
            $pay_frequency,
            $user_id,
            $ctc_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }
    } else {

        /* Insert CTC Component */
        $stmt = $pdo->prepare("INSERT INTO ctc_creation
            (
                company_id,
                salary_component,
                component_classification,
                component_category,
                pay_frequency,
                insert_login_id
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?
            )
        ");

        $qry = $stmt->execute([
            $company_id,
            $salary_component,
            $component_classification,
            $component_category,
            $pay_frequency,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
