
<?php
require '../../ajaxconfig.php';

$id = $_POST['id'] ?? '';
$response = [];

try {
    $stmt = $pdo->prepare("
        WITH RECURSIVE month_list AS (
            SELECT
                id AS staff_loan_id,
                DATE_FORMAT(due_start_date, '%Y-%m-01') AS deduction_month,
                due_amount
            FROM staff_loan
            WHERE id = ?
              AND due_start_date IS NOT NULL

            UNION ALL

            SELECT
                ml.staff_loan_id,
                DATE_ADD(ml.deduction_month, INTERVAL 1 MONTH),
                ml.due_amount
            FROM month_list ml
            WHERE DATE_ADD(ml.deduction_month, INTERVAL 1 MONTH)
                < DATE_FORMAT(CURDATE(), '%Y-%m-01')
        ),
        deduction_summary AS (
            SELECT
                staff_loan_id,
                SUM(deduction_amount) AS total_deducted
            FROM staff_deduction
            WHERE deduction_month <= DATE_FORMAT(CURDATE(), '%Y-%m-01')
            GROUP BY staff_loan_id
        ),
        automatic_summary AS (
            SELECT
                ml.staff_loan_id,
                SUM(ml.due_amount) AS scheduled_amount
            FROM month_list ml
            LEFT JOIN staff_deduction sd
                ON sd.staff_loan_id = ml.staff_loan_id
                AND DATE_FORMAT(sd.deduction_month, '%Y-%m-01') = ml.deduction_month
            WHERE sd.id IS NULL
            AND ml.deduction_month < DATE_FORMAT(CURDATE(), '%Y-%m-01')
            GROUP BY ml.staff_loan_id
        )
        SELECT
            sl.*,
            COALESCE(ds.total_deducted, 0) AS total_deducted,
            GREATEST(
                0,
                sl.loan_amount + sl.total_intrest
                - COALESCE(ds.total_deducted, 0)
                - COALESCE(ats.scheduled_amount, 0)
            ) AS balance_amount
        FROM staff_loan sl
        LEFT JOIN deduction_summary ds
            ON ds.staff_loan_id = sl.id
        LEFT JOIN automatic_summary ats
            ON ats.staff_loan_id = sl.id
        WHERE sl.id = ?
    ");

    $stmt->execute([$id, $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = $result;
    }
} catch (PDOException $e) {
    $response = ['error' => $e->getMessage()];
}

$pdo = null;

echo json_encode($response);
?>
