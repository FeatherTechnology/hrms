 
<?php
include '../../ajaxconfig.php';

$result = array();

$company_id = $_POST['company_id'] ?? '';

if (!empty($company_id)) {

    // If company_id is already an array
    if (is_array($company_id)) {
        $companyIds = array_map('intval', $company_id);
    } 
    // If company_id comes as "1,2,3"
    else {
        $companyIds = array_map('intval', explode(',', $company_id));
    }

    // Remove empty/invalid values
    $companyIds = array_filter($companyIds);

    if (!empty($companyIds)) {

        $placeholders = implode(
            ',',
            array_fill(0, count($companyIds), '?')
        );

        $stmt = $pdo->prepare("
            SELECT 
                id,
                branch_name
            FROM branch_creation
            WHERE company_id IN ($placeholders)
            ORDER BY branch_name
        ");

        $stmt->execute(array_values($companyIds));

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$pdo = null;

echo json_encode($result);
?>
 
