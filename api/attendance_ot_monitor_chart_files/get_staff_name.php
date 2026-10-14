<?php
require '../../ajaxconfig.php';

$response = array();

$company_id    = $_POST['company_id'] ?? '';
$branch_id     = $_POST['branch_id'] ?? '';
$department_id = $_POST['department_id'] ?? '';
$shift_id      = $_POST['shift_id'] ?? '';

try {

    $sql = "SELECT oi.staff_profile_id AS id, sc.staff_name
            FROM staff_creation sc
            LEFT JOIN occupation_info oi
                ON oi.id = (
                    SELECT MAX(id)
                    FROM occupation_info
                    WHERE staff_profile_id = sc.id
                      AND effective_from <= NOW()
                )
            WHERE oi.company_id = :company_id
              AND oi.shift = :shift_id";

    $params = [
        ':company_id' => $company_id,
        ':shift_id'   => $shift_id
    ];

    // Add branch condition only when branch has a value
    if ($branch_id !== '') {
        $sql .= " AND oi.branch_id = :branch_id";
        $params[':branch_id'] = $branch_id;
    }

    // Add department condition only when department has a value
    if ($department_id !== '') {
        $sql .= " AND oi.department = :department_id";
        $params[':department_id'] = $department_id;
    }

    $qry = $pdo->prepare($sql);
    $qry->execute($params);

    if ($qry->rowCount() > 0) {
        $response = $qry->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

$pdo = null;

echo json_encode($response);
