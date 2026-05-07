<?php
require '../../ajaxconfig.php';
$company_id = $_POST['company_id'];
$qry = $pdo->query("SELECT * FROM ctc_info where company_id = '$company_id' ORDER BY id ASC");

$data = array();

while($row = $qry->fetch(PDO::FETCH_ASSOC)) {

    $classification = '';
    $category = '';

    if($row['component_classification'] == '1') {
        $classification = 'CTC';
    } else {
        $classification = 'NON CTC';
    }

    if($row['component_cat'] == '1') {
        $category = 'Salary';
    } else {
        $category = 'Reimbursement';
    }

    $data[] = array(
        'id' => $row['id'],
        'salary_component' => $row['salary_component'],
        'component_classification' => $classification,
        'component_cat' => $category
    );
}

echo json_encode($data);
?>