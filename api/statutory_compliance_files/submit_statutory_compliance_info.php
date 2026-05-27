<?php
require '../../ajaxconfig.php';
@session_start();

$company_id = $_POST['company_id'];
$state = $_POST['state'];
$pf_applicable = $_POST['pf_applicable'];
$pf_number = $_POST['pf_number'];
$employee_contribution = $_POST['employee_contribution'];
$employer_contribution = $_POST['employer_contribution'];
$admin_charge = $_POST['admin_charge'];
$pension = $_POST['pension'];
$apply_wage_limit = $_POST['apply_wage_limit'];
$pf_wage_limit = $_POST['pf_wage_limit'];
$esi_applicable = $_POST['esi_applicable'];
$employee_share = $_POST['employee_share'];
$employer_share = $_POST['employer_share'];
$professional_tax_applicable = $_POST['professional_tax_applicable'];
$calculation_type = $_POST['calculation_type'];
$percentage = $_POST['percentage'];
$slab = $_POST['slab'];
$statutory_compliance_id = $_POST['statutory_compliance_id'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($statutory_compliance_id != '') {
    $qry = $pdo->query("UPDATE `statutory_compliance` SET `company_id`='$company_id', `state`='$state', `pf_applicable`='$pf_applicable', `pf_number`='$pf_number', `employee_contribution`='$employee_contribution', `employer_contribution`='$employer_contribution', `admin_charge`='$admin_charge', `pension`='$pension', `apply_wage_limit`='$apply_wage_limit', `pf_wage_limit`='$pf_wage_limit', `esi_applicable`='$esi_applicable', `employee_share`='$employee_share', `employer_share`='$employer_share', `professional_tax_applicable`='$professional_tax_applicable', `calculation_type`='$calculation_type', `percentage`='$percentage', `slab`='$slab', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$statutory_compliance_id'");

    if ($qry) {
        $result = 1; // Update successfull
    }
} else {
    $qry = $pdo->query("INSERT INTO `statutory_compliance`(`company_id`, `state`, `pf_applicable`, `pf_number`, `employee_contribution`, `employer_contribution`, `admin_charge`, `pension`, `apply_wage_limit`, `pf_wage_limit`, `esi_applicable`, `employee_share`, `employer_share`, `professional_tax_applicable`, `calculation_type`, `percentage`, `slab`, `insert_login_id`) VALUES ('$company_id', '$state', '$pf_applicable', '$pf_number', '$employee_contribution', '$employer_contribution', '$admin_charge', '$pension', '$apply_wage_limit', '$pf_wage_limit', '$esi_applicable', '$employee_share', '$employer_share', '$professional_tax_applicable', '$calculation_type', '$percentage', '$slab', '$user_id')");

    if ($qry) {
        $result = 2; // Insert successfull
    }
}

$pdo = null; // Close Connection

echo json_encode($result);