<?php

/** Statutory Compliance Save **
 * Purpose:
 * - Inserts a new statutory compliance record.
 * - Updates an existing statutory compliance record.
 * - Stores PF, ESI, and Professional Tax configurations.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 */

require '../../ajaxconfig.php';
@session_start();

$company_id                    = $_POST['company_id'];
$state                         = $_POST['state'];
$pf_applicable                 = $_POST['pf_applicable'];
$pf_number                     = $_POST['pf_number'];
$employee_contribution         = $_POST['employee_contribution'];
$employer_contribution         = $_POST['employer_contribution'];
$admin_charge                  = $_POST['admin_charge'];
$pension                       = $_POST['pension'];
$apply_wage_limit              = $_POST['apply_wage_limit'];
$pf_wage_limit                 = $_POST['pf_wage_limit'];
$esi_applicable                = $_POST['esi_applicable'];
$employee_share                = $_POST['employee_share'];
$employer_share                = $_POST['employer_share'];
$professional_tax_applicable   = $_POST['professional_tax_applicable'];
$calculation_type              = $_POST['calculation_type'];
$percentage                    = $_POST['percentage'];
$slab                          = $_POST['slab'];
$statutory_compliance_id       = $_POST['statutory_compliance_id'];
$user_id                       = $_SESSION['user_id'];

$result = 0;

if (!empty($statutory_compliance_id)) {

    /* Update Statutory Compliance */
    $stmt = $pdo->prepare("UPDATE statutory_compliance
        SET
            company_id = ?,
            state = ?,
            pf_applicable = ?,
            pf_number = ?,
            employee_contribution = ?,
            employer_contribution = ?,
            admin_charge = ?,
            pension = ?,
            apply_wage_limit = ?,
            pf_wage_limit = ?,
            esi_applicable = ?,
            employee_share = ?,
            employer_share = ?,
            professional_tax_applicable = ?,
            calculation_type = ?,
            percentage = ?,
            slab = ?,
            update_login_id = ?,
            updated_date = NOW()
        WHERE id = ?
    ");

    $qry = $stmt->execute([
        $company_id,
        $state,
        $pf_applicable,
        $pf_number,
        $employee_contribution,
        $employer_contribution,
        $admin_charge,
        $pension,
        $apply_wage_limit,
        $pf_wage_limit,
        $esi_applicable,
        $employee_share,
        $employer_share,
        $professional_tax_applicable,
        $calculation_type,
        $percentage,
        $slab,
        $user_id,
        $statutory_compliance_id
    ]);

    if ($qry) {
        $result = 1; // Update Successful
    }
} else {

    /* Insert Statutory Compliance */
    $stmt = $pdo->prepare("INSERT INTO statutory_compliance
        (
            company_id,
            state,
            pf_applicable,
            pf_number,
            employee_contribution,
            employer_contribution,
            admin_charge,
            pension,
            apply_wage_limit,
            pf_wage_limit,
            esi_applicable,
            employee_share,
            employer_share,
            professional_tax_applicable,
            calculation_type,
            percentage,
            slab,
            insert_login_id
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $qry = $stmt->execute([
        $company_id,
        $state,
        $pf_applicable,
        $pf_number,
        $employee_contribution,
        $employer_contribution,
        $admin_charge,
        $pension,
        $apply_wage_limit,
        $pf_wage_limit,
        $esi_applicable,
        $employee_share,
        $employer_share,
        $professional_tax_applicable,
        $calculation_type,
        $percentage,
        $slab,
        $user_id
    ]);

    if ($qry) {
        $result = 2; // Insert Successful
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
