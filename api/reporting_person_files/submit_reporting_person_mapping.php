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

    // // MAKE REPORTING STAFF ARRAY
    // if (!is_array($reporting_staff)) {
    //     $reporting_staff = [$reporting_staff];
    // }

    // // VALIDATE STAFF IDs Only numeric staff_creation IDs are allowed.
    // $reporting_staff = array_filter(
    //     $reporting_staff,
    //     function ($value) {
    //         return is_numeric($value) && (int)$value > 0;
    //     }
    // );

    // // Convert values to integer
    // $reporting_staff = array_map('intval', $reporting_staff);

    // // Remove duplicate staff IDs
    // $reporting_staff = array_values(array_unique($reporting_staff));

    // // EXISTING STAFF IDs Example:reporting_staff2 = "12||15||20"
    // if (!empty($reporting_staff2)) {

    //     $old_reporting_staff = explode('||', $reporting_staff2);

    //     // Keep only valid numeric IDs
    //     $old_reporting_staff = array_filter(
    //         $old_reporting_staff,
    //         function ($value) {
    //             return is_numeric($value) && (int)$value > 0;
    //         }
    //     );

    //     // Convert to integer
    //     $old_reporting_staff = array_map('intval', $old_reporting_staff);

    //     // Remove duplicates
    //     $old_reporting_staff = array_values(array_unique($old_reporting_staff));
    // } else {

    //     $old_reporting_staff = [];
    // }

    // // VALIDATE SELECTED STAFF EXISTS This prevents invalid staff IDs from being inserted.
    // if (!empty($reporting_staff)) {

    //     $placeholders = implode(',', array_fill(0, count($reporting_staff), '?'));

    //     $stmt = $pdo->prepare("SELECT id FROM staff_creation WHERE id IN ($placeholders) AND company_id = ? AND status = 1");

    //     $params = $reporting_staff;
    //     $params[] = $company_name;

    //     $stmt->execute($params);

    //     $valid_staff_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    //     // Convert database IDs to integer
    //     $valid_staff_ids = array_map('intval', $valid_staff_ids);

    //     // Keep only valid staff IDs
    //     $reporting_staff = array_values(array_intersect($reporting_staff, $valid_staff_ids));
    // }


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
