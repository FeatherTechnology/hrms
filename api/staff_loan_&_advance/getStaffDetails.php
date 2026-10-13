<?php
require '../../ajaxconfig.php';

$id = $_POST['id'] ?? '';
$response = [];

try {
    $stmt = $pdo->prepare("
        SELECT
            sl.*,
            COALESCE(sd.total_deducted, 0) AS total_deducted,
            GREATEST(
                0,
                sl.loan_amount + sl.total_intrest
                - COALESCE(sd.total_deducted, 0)
            ) AS balance_amount
        FROM staff_loan sl
        LEFT JOIN (
            SELECT
                staff_loan_id,
                SUM(deduction_amount) AS total_deducted
            FROM staff_deduction
            GROUP BY staff_loan_id
        ) sd ON sd.staff_loan_id = sl.id
        WHERE sl.id = ?
    ");

    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = $result;
    }
} catch (PDOException $e) {
    $response = [
        'error' => $e->getMessage()
    ];
}

$pdo = null;

echo json_encode($response);
?>