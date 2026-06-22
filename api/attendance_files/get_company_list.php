<?php
//  to get the company list  based on the user id (director or employee)

include '../../ajaxconfig.php';
session_start();

$userid = $_SESSION['user_id'] ?? '';

$result = [];

if (!empty($userid)) {

    // Get user details
    $stmt = $pdo->prepare("
        SELECT user_type, director_company, company_id
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$userid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if ($user['user_type'] == 1) {

            // director_company contains values like: 1,2,3
            $companyIds = $user['director_company'];

            if (!empty($companyIds)) {

                $qry = $pdo->query("
                    SELECT id, company_name
                    FROM company_creation
                    WHERE id IN ($companyIds)
                ");

                $result = $qry->fetchAll(PDO::FETCH_ASSOC);
            }

        } else if ($user['user_type'] == 2) {

            // Current logic
            $stmt = $pdo->prepare("
                SELECT cc.id, cc.company_name
                FROM company_creation cc
                LEFT JOIN users u ON u.company_id = cc.id
                WHERE u.id = ?
            ");
            $stmt->execute([$userid]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

$pdo = null;

echo json_encode($result);