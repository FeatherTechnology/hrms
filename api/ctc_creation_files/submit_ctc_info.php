<?php
require '../../ajaxconfig.php';
@session_start();

$company_id = $_POST['company_id'];
$ctc_id = $_POST['ctc_id'];
$salary_component = $_POST['salary_component'];
$component_classification = $_POST['component_classification'];
$component_category = $_POST['component_category'];
$pay_frequency = $_POST['pay_frequency'];
$user_id = $_SESSION['user_id'];


$result = 0;
$qry = $pdo->query("SELECT * FROM `ctc_creation` WHERE REPLACE(TRIM(salary_component), ' ', '') = REPLACE(TRIM('$salary_component'), ' ', '') AND salary_component= '$salary_component' AND component_classification = '$component_classification' AND component_category = '$component_category' AND pay_frequency = '$pay_frequency' AND status = 0 ");
if ($qry->rowCount() > 0) {
    $result = 3; //already Exists.

} else {
    if ($ctc_id != '') {
        $qry = $pdo->query("UPDATE `ctc_creation` SET `company_id`='$company_id', `salary_component`='$salary_component', `component_classification`='$component_classification', `component_category`='$component_category', `pay_frequency`='$pay_frequency', `update_login_id`='$user_id', updated_date = now() WHERE `id`='$ctc_id'");

        if ($qry) {
            $result = 1; // Update successfull
        }
    } else {
        $qry = $pdo->query("INSERT INTO `ctc_creation`(`company_id`, `salary_component`, `component_classification`, `component_category`, `pay_frequency` , `insert_login_id`) VALUES ('$company_id', '$salary_component', '$component_classification', '$component_category', '$pay_frequency' , '$user_id')");

        if ($qry) {
            $result = 2; // Insert successfull
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);