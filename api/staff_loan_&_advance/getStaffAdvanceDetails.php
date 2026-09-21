<!-- to get the staff advance staff details -->
<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$response = array();

try {
    $stmt = $pdo->prepare("SELECT ssa.* 
    FROM `staff_salary_adavance` ssa 
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = ssa.staff_id AND effective_from <= CURDATE()
        )
    LEFT JOIN branch_creation bc ON oi.branch_id = bc.id
    LEFT JOIN department_creation dc ON oi.department = dc.id
    LEFT JOIN team_name_creation tnc ON oi.team = tnc.id
    LEFT JOIN designation_creation ds ON oi.designation = ds.id
    LEFT JOIN company_creation cc ON ssa.company_id = cc.id
    LEFT JOIN staff_creation sc ON ssa.staff_id = sc.id
    WHERE ssa.id= ?
    ");
    $stmt->execute([$id ]);

    if ($stmt->rowCount() > 0) {
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {

    $response['error'] = $e->getMessage();
}

$pdo = null;
echo json_encode($response);
