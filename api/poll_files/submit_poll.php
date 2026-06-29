<?php

require '../../ajaxconfig.php';
@session_start();

try {

    $pdo->beginTransaction();

    $poll_company_name      = $_POST['poll_company_name'];
    $poll_department_name   = isset($_POST['poll_department_name']) ? $_POST['poll_department_name'] : [];
    $poll_department_name2  = isset($_POST['poll_department_name2']) ? explode(',', $_POST['poll_department_name2']) : [];
    $poll_start_date        = $_POST['poll_start_date'];
    $poll_end_date          = $_POST['poll_end_date'];
    $poll_title             = trim($_POST['poll_title']);
    $poll_description       = $_POST['poll_description']; 
    $poll_options           = isset($_POST['poll_options']) ? $_POST['poll_options'] : [];

    $user_id                = $_SESSION['user_id'];
    $poll_titles_id         = $_POST['poll_titles_id'];

    $result = 0;

    /*--- UPDATE poll_titles ---*/
    if ($poll_titles_id != '') {

        $stmt = $pdo->prepare("UPDATE poll_titles 
            SET 
                company_id              = ?,
                start_date_time         = ?,
                end_date_time           = ?,
                poll_title              = ?,
                poll_description        = ?, 
                update_login_id         = ?,
                updated_date            = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $poll_company_name,
            $poll_start_date,
            $poll_end_date,
            $poll_title,
            $poll_description, 
            $user_id,
            $poll_titles_id
        ]);

        /*--- Department Update ---*/
        $poll_department_name = array_map('intval', $poll_department_name);
        $poll_department_name2 = array_map('intval', $poll_department_name2);

        $department_to_delete = array_diff($poll_department_name2, $poll_department_name);
        $department_to_insert = array_diff($poll_department_name, $poll_department_name2);

        foreach ($department_to_delete as $department_del_id) {

            $stmt = $pdo->prepare("DELETE FROM poll_department_mapping WHERE poll_titles_id = ? AND department_id = ?");

            $stmt->execute([$poll_titles_id, $department_del_id]);
        }

        foreach ($department_to_insert as $department_new_id) {

            $stmt = $pdo->prepare("INSERT INTO poll_department_mapping (poll_titles_id, department_id) VALUES (?, ?)");

            $stmt->execute([$poll_titles_id, $department_new_id]);
        }

        /*--- Delete Old Options ---*/
        $stmt = $pdo->prepare("DELETE FROM poll_options_mapping WHERE poll_titles_id = ?");

        $stmt->execute([$poll_titles_id]);

        $result = 1;
    } else {

        /*--- INSERT poll_titles ---*/

        $stmt = $pdo->prepare("INSERT INTO poll_titles
            (
                company_id,
                start_date_time,
                end_date_time,
                poll_title,
                poll_description,
                poll_status,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ");

        $stmt->execute([
            $poll_company_name,
            $poll_start_date,
            $poll_end_date,
            $poll_title,
            $poll_description,
            0,
            $user_id
        ]);

        $poll_titles_id = $pdo->lastInsertId();

        foreach ($poll_department_name as $department_id) {

            $department_id = (int)$department_id;

            if ($department_id > 0) {

                $stmt = $pdo->prepare("INSERT INTO poll_department_mapping (poll_titles_id, department_id) VALUES (?, ?)");

                $stmt->execute([$poll_titles_id, $department_id]);
            }
        }

        $result = 2;
    }

    /*--- Insert poll_options_mapping ---*/
    foreach ($poll_options as $option) {

        $option = trim($option);

        if ($option != '') {

            $stmt = $pdo->prepare("INSERT INTO poll_options_mapping (poll_titles_id, poll_options) VALUES (?, ?)");

            $stmt->execute([$poll_titles_id, $option]);
        }
    }

    $pdo->commit();

    echo json_encode($result);
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
