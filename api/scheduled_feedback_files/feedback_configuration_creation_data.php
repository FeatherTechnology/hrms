<?php

/** Fetch Feedback Configuration Details **
 * Purpose:
 * - Retrieves feedback configuration information based on the provided feedback ID.
 * - Fetches mapped department IDs.
 * - Fetches feedback questions.
 * - Converts department IDs and feedback questions into arrays.
 * - Returns feedback configuration details in JSON format for edit/view screens.
 */

require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = [];

$stmt = $pdo->prepare("SELECT
        ft.company_id,
        ft.start_date_time,
        ft.end_date_time,
        ft.feedback_title,
        ft.feedback_status,
        GROUP_CONCAT(DISTINCT fdm.department_id) AS department_ids,
        GROUP_CONCAT(
            DISTINCT fqm.feedback_questions
            SEPARATOR '||'
        ) AS feedback_questions
    FROM feedback_titles ft
    LEFT JOIN feedback_department_mapping fdm
        ON ft.id = fdm.feedback_titles_id
    LEFT JOIN feedback_questions_mapping fqm
        ON ft.id = fqm.feedback_titles_id
    WHERE ft.id = ?
    GROUP BY ft.id
");

$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $result['department_ids'] = !empty($result['department_ids'])
        ? explode(',', $result['department_ids'])
        : [];

    $result['feedback_questions'] = !empty($result['feedback_questions'])
        ? explode('||', $result['feedback_questions'])
        : [];
}

$pdo = null; // Close Connection

echo json_encode($result);
