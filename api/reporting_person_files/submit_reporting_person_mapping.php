<?php

require '../../ajaxconfig.php';
@session_start();

try {

    $pdo->beginTransaction();

    $company_name       = $_POST['company_name'] ?? '';
    $user_type          = $_POST['user_type'] ?? '';
    $designation        = !empty($_POST['designation']) ? (int)$_POST['designation'] : null;
    $reporting_person   = !empty($_POST['reporting_person']) ? (int)$_POST['reporting_person'] : null;

    // Selected staff IDs from Choices.js
    $reporting_staff = $_POST['reporting_staff'] ?? [];

    // Existing staff IDs during edit
    // Example: 12||15||20
    $reporting_staff2 = $_POST['reporting_staff2'] ?? '';

    $director_name = !empty($_POST['director_name']) ? (int)$_POST['director_name'] : null;
    $reporting_person_id = $_POST['reporting_person_id'] ?? '';

    $user_id = $_SESSION['user_id'] ?? 0;


    // Update Reporting Person
    if (!empty($reporting_person_id)) {

        $stmt = $pdo->prepare("UPDATE reporting_person
            SET
                company_id = ?,
                user_type = ?,
                designation = ?,
                reporting_person = ?,
                director_id = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $stmt->execute([

            $company_name,
            $user_type,
            $designation,
            $reporting_person,
            $director_name,
            $user_id,
            $reporting_person_id

        ]);

        // REMOVE ALL OLD STAFF MAPPINGS
        $stmt = $pdo->prepare("DELETE FROM reporting_person_mapping WHERE reporting_person_id = ?");

        $stmt->execute([
            $reporting_person_id
        ]);

        // INSERT CURRENT STAFF MAPPINGS
        if (!empty($reporting_staff)) {

            $stmt = $pdo->prepare("INSERT INTO reporting_person_mapping(reporting_person_id,reporting_staff)VALUES(?,?)");

            foreach ($reporting_staff as $staff_id) {

                $stmt->execute([
                    $reporting_person_id,
                    $staff_id
                ]);
            }
        }


        $result = 2;
    }

    // Insert Reporting Person
    else {

        $stmt = $pdo->prepare("INSERT INTO reporting_person
            (
                company_id,
                user_type,
                designation,
                reporting_person,
                director_id,
                insert_login_id,
                created_date
            )
            VALUES
            ( ?, ?,  ?, ?, ?, ?, NOW() )
        ");

        $stmt->execute([

            $company_name,
            $user_type,
            $designation,
            $reporting_person,
            $director_name,
            $user_id

        ]);

        // Get Reporting Person ID
        $reporting_person_id = $pdo->lastInsertId();

        // Insert Reporting Staff IDs
        if (!empty($reporting_staff)) {

            $stmt = $pdo->prepare("INSERT INTO reporting_person_mapping
                (
                    reporting_person_id,
                    reporting_staff
                )
                VALUES
                (
                    ?,
                    ?
                )
            ");

            foreach ($reporting_staff as $staff_id) {

                $stmt->execute([

                    $reporting_person_id,
                    $staff_id

                ]);
            }
        }


        $result = 1;
    }

    $pdo->commit();

    echo json_encode($result);
} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }

    echo json_encode([

        'status'  => false,
        'message' => $e->getMessage()

    ]);
}
