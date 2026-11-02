
<?php
session_start();
require "../../ajaxconfig.php";
$response = [
    'status' => 0,
    'message' => 'Unable to check staff loan balance.',
    'loans' => []
];

try {
    $staff_id = (int)($_POST['staff_id'] ?? 0);

    if ($staff_id <= 0) {
        throw new Exception('Invalid staff ID.');
    }

    $stmt = $pdo->prepare("
        WITH RECURSIVE month_list AS (
            SELECT
                sl.id AS staff_loan_id,
                DATE_FORMAT(sl.due_start_date, '%Y-%m-01') AS deduction_month,
                sl.due_amount
            FROM staff_loan sl
            WHERE sl.staff_id = ?
              AND sl.due_start_date IS NOT NULL

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
                sd.staff_loan_id,
                SUM(sd.deduction_amount) AS total_deducted
            FROM staff_deduction sd
            INNER JOIN staff_loan sl
                ON sl.id = sd.staff_loan_id
            WHERE sl.staff_id = ?
              AND sd.deduction_month <= DATE_FORMAT(CURDATE(), '%Y-%m-01')
            GROUP BY sd.staff_loan_id
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
            sl.id,
            sl.type,
            sl.loan_amount,
            sl.total_intrest,
            COALESCE(ds.total_deducted, 0) AS total_deducted,
            GREATEST(
                0,
                COALESCE(sl.loan_amount, 0)
                + COALESCE(sl.total_intrest, 0)
                - COALESCE(ds.total_deducted, 0)
                - COALESCE(ats.scheduled_amount, 0)
            ) AS balance_amount
        FROM staff_loan sl
        LEFT JOIN deduction_summary ds
            ON ds.staff_loan_id = sl.id
        LEFT JOIN automatic_summary ats
            ON ats.staff_loan_id = sl.id
        WHERE sl.staff_id = ?
          AND sl.type IN (1, 2)
        ORDER BY sl.type, sl.id
    ");

    $stmt->execute([$staff_id, $staff_id, $staff_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $loans = [];


    foreach ($rows as $row) {
        $balance = (float)$row['balance_amount'];

        if ($balance <= 0) {
            continue;
        }

        $type = (int)$row['type'];

        $loans[] = [
            'id' => (int)$row['id'],
            'loan_type' => $type === 1 ? 'Loan' : 'Advance',
            'loan_amount' => number_format((float)$row['loan_amount'], 2),
            'total_deducted' => number_format((float)$row['total_deducted'], 2),
            'balance_amount' => number_format($balance, 2)
        ];
    }

    $response = [
        'status' => 1,
        'message' => 'Staff loan balance checked successfully.',
        'loans' => $loans
    ];
} catch (Throwable $e) {
    $response = [
        'status' => 0,
        'message' => $e instanceof PDOException
            ? 'Database error while checking staff loan balance.'
            : $e->getMessage(),
        'loans' => []
    ];
}

echo json_encode($response);
?>
