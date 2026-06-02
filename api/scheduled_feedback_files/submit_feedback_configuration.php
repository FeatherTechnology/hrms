<?php

require '../../ajaxconfig.php';
@session_start();

try {

    $pdo->beginTransaction();

    $feedback_config_company_name      = $_POST['feedback_config_company_name'];
    $feedback_config_department_name   = isset($_POST['feedback_config_department_name']) ? $_POST['feedback_config_department_name'] : [];
    $feedback_config_department_name2  = isset($_POST['feedback_config_department_name2']) ? explode(',', $_POST['feedback_config_department_name2']) : [];
    $feedback_config_start_date        = $_POST['feedback_config_start_date'];
    $feedback_config_end_date          = $_POST['feedback_config_end_date'];
    $feedback_title                    = trim($_POST['feedback_title']);
    $feedback_status                   = $_POST['feedback_status'];
    $feedback_questions                = isset($_POST['feedback_questions']) ? $_POST['feedback_questions'] : [];

    $user_id                           = $_SESSION['user_id'];
    $feedback_titles_id         = $_POST['feedback_titles_id'];

    $result = 0;

    /*--------------------------------------------------------------- UPDATE ---------------------------------------------------------------*/

    if ($feedback_titles_id != '') {

        $stmt = $pdo->prepare("UPDATE feedback_titles 
            SET 
                company_id              = ?,
                start_date_time         = ?,
                end_date_time           = ?,
                feedback_title          = ?,
                feedback_status         = ?,
                update_login_id          = ?,
                updated_date            = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $feedback_config_company_name,
            $feedback_config_start_date,
            $feedback_config_end_date,
            $feedback_title,
            $feedback_status,
            $user_id,
            $feedback_titles_id
        ]);

        /*--------------------------------------------------------------- Department Update ---------------------------------------------------------------*/

        $feedback_config_department_name = array_map('intval', $feedback_config_department_name);
        $feedback_config_department_name2 = array_map('intval', $feedback_config_department_name2);

        $department_to_delete = array_diff($feedback_config_department_name2, $feedback_config_department_name);
        $department_to_insert = array_diff($feedback_config_department_name, $feedback_config_department_name2);

        foreach ($department_to_delete as $department_del_id) {

            $stmt = $pdo->prepare("DELETE FROM feedback_department_mapping WHERE feedback_titles_id = ? AND department_id = ?");

            $stmt->execute([
                $feedback_titles_id,
                $department_del_id
            ]);
        }

        foreach ($department_to_insert as $department_new_id) {

            $stmt = $pdo->prepare("INSERT INTO feedback_department_mapping (feedback_titles_id, department_id) VALUES (?, ?)");

            $stmt->execute([
                $feedback_titles_id,
                $department_new_id
            ]);
        }

        /*--------------------------------------------------------------- Delete Old Questions ---------------------------------------------------------------*/

        $stmt = $pdo->prepare("DELETE FROM feedback_questions_mapping WHERE feedback_titles_id = ?");

        $stmt->execute([$feedback_titles_id]);
    } else {

        /*-------------------------------------------------------------- INSERT ---------------------------------------------------------------*/

        $stmt = $pdo->prepare("INSERT INTO feedback_titles
            (
                company_id,
                start_date_time,
                end_date_time,
                feedback_title,
                feedback_status,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, NOW()
            )
        ");

        $stmt->execute([
            $feedback_config_company_name,
            $feedback_config_start_date,
            $feedback_config_end_date,
            $feedback_title,
            $feedback_status,
            $user_id
        ]);

        $feedback_titles_id = $pdo->lastInsertId();

        foreach ($feedback_config_department_name as $department_id) {

            $department_id = (int)$department_id;

            if ($department_id > 0) {

                $stmt = $pdo->prepare("INSERT INTO feedback_department_mapping (feedback_titles_id, department_id) VALUES (?, ?)");

                $stmt->execute([
                    $feedback_titles_id,
                    $department_id
                ]);
            }
        }
    }

    /*------------------------------------------------------------- Insert Questions ---------------------------------------------------------------*/

    foreach ($feedback_questions as $question) {

        $question = trim($question);

        if ($question != '') {

            $stmt = $pdo->prepare("INSERT INTO feedback_questions_mapping (feedback_titles_id, feedback_questions) VALUES (?, ?)");

            $stmt->execute([
                $feedback_titles_id,
                $question
            ]);
        }
    }

    $result = 2;

    $pdo->commit();

    echo json_encode($result);
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
