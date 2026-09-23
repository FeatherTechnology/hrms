<?php
// <!-- to get the staff details  -->
require '../../ajaxconfig.php';

$id = $_POST['id'];

$response = array();

try {

    $stmt = $pdo->prepare("SELECT sl.* 
    FROM `staff_loan` sl 
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = sl.staff_id
        )
    LEFT JOIN branch_creation bc ON oi.branch_id = bc.id
    LEFT JOIN department_creation dc ON oi.department = dc.id
    LEFT JOIN team_name_creation tnc ON oi.team = tnc.id
    LEFT JOIN designation_creation ds ON oi.designation = ds.id
    LEFT JOIN company_creation cc ON sl.company_id = cc.id
    LEFT JOIN staff_creation sc ON sl.staff_id = sc.id
    WHERE sl.id= ?
        
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
