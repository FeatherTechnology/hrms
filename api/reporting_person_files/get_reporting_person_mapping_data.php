<?php

require '../../ajaxconfig.php';

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode([]);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT

            rp.id AS reporting_person_id,
            rp.company_id,
            rp.user_type,
            rp.designation,
            rp.reporting_person,
            rp.director_id,
            cc.company_name,
            dc.designation AS designation_name,
            sc.staff_name AS reporting_person_name,
            dir.director_name,

            GROUP_CONCAT(
                rpm.reporting_staff
                ORDER BY rpm.id
                SEPARATOR '||'
            ) AS reporting_staff2

        FROM reporting_person rp

        LEFT JOIN company_creation cc ON cc.id = rp.company_id
        LEFT JOIN designation_creation dc ON dc.id = rp.designation
        LEFT JOIN staff_creation sc ON sc.id = rp.reporting_person AND sc.company_id = rp.company_id
        LEFT JOIN director_creation dir ON dir.id = rp.director_id
        LEFT JOIN reporting_person_mapping rpm ON rpm.reporting_person_id = rp.id
        WHERE rp.id = ?

        GROUP BY
            rp.id,
            rp.company_id,
            rp.user_type,
            rp.designation,
            rp.reporting_person,
            rp.director_id,
            cc.company_name,
            dc.designation,
            sc.staff_name,
            dir.director_name
    ");

    $stmt->execute([
        $id
    ]);

    $result = $stmt->fetch(
        PDO::FETCH_ASSOC
    );

    if (!$result) {
        echo json_encode([]);
        exit;
    }

    $response = [

        'reporting_person_id' => $result['reporting_person_id'],
        'company_id' => $result['company_id'],
        'user_type' => $result['user_type'],
        'designation' => $result['designation'],
        'reporting_person' => $result['reporting_person'],
        'director_id' => $result['director_id'],
        'reporting_staff2' => $result['reporting_staff2'] ?? ''
    ];


    echo json_encode([
        $response
    ]);
} catch (Exception $e) {

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
