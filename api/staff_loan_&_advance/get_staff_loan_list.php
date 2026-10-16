<?php
require '../../ajaxconfig.php';
@session_start();

$staff_Loan_list = [];
$user_id = $_SESSION['user_id'] ?? '';

$userStmt = $pdo->prepare("
    SELECT user_type, director_company, company_id
    FROM users
    WHERE id = ?
");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $sql = "
        SELECT
            sl.*,
            cc.company_name,
            sc.staff_name
        FROM staff_loan sl
        LEFT JOIN company_creation cc ON cc.id = sl.company_id
        LEFT JOIN staff_creation sc ON sc.id = sl.staff_id
    ";

    $params = [];

    if ($user['user_type'] == 1) {
        $company_ids = array_values(array_filter(
            array_map('trim', explode(',', $user['director_company'] ?? '')),
            'ctype_digit'
        ));

        if (!empty($company_ids)) {
            $placeholders = implode(',', array_fill(0, count($company_ids), '?'));
            $sql .= " WHERE sl.company_id IN ($placeholders)";
            $params = $company_ids;
        } else {
            $sql .= " WHERE 1 = 0";
        }
    } elseif ($user['user_type'] == 2) {
        $sql .= " WHERE sl.company_id = ?";
        $params[] = $user['company_id'];
    } else {
        $sql .= " WHERE 1 = 0";
    }

    $sql .= " ORDER BY sl.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $deductionStmt = $pdo->prepare("
        SELECT deduction_month, deduction_amount
        FROM staff_deduction
        WHERE staff_loan_id = ?
          AND deduction_month < DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
        ORDER BY deduction_month ASC
    ");

    $currentMonth = date('Y-m');

    foreach ($loans as $staffLoan) {
        $totalPayable = (float)$staffLoan['loan_amount']
            + (float)$staffLoan['total_intrest'];

        $balance = $totalPayable;
        $deductions = [];

        $deductionStmt->execute([$staffLoan['id']]);

        while ($deduction = $deductionStmt->fetch(PDO::FETCH_ASSOC)) {
            $month = date('Y-m', strtotime($deduction['deduction_month']));
            $deductions[$month] = (float)$deduction['deduction_amount'];
        }

        if (!empty($staffLoan['due_start_date'])) {
            $startMonth = new DateTime(
                date('Y-m-01', strtotime($staffLoan['due_start_date']))
            );

            $currentDate = new DateTime(date('Y-m-01'));

            while ($startMonth <= $currentDate && $balance > 0) {
                $month = $startMonth->format('Y-m');

                if ($month < $currentMonth) {
                    if (array_key_exists($month, $deductions)) {
                        $amount = $deductions[$month];
                    } else {
                        $amount = (float)$staffLoan['due_amount'];
                    }

                    $balance = max(0, $balance - $amount);
                } elseif ($month === $currentMonth) {
                    if (array_key_exists($month, $deductions)) {
                        $balance = max(0, $balance - $deductions[$month]);
                    }
                }

                $startMonth->modify('+1 month');
            }
        }

        if ($balance <= 0) {
            continue;
        }

        $staffLoan['total_deducted'] = round($totalPayable - $balance, 2);
        $staffLoan['balance_amount'] = round($balance, 2);

        $staffLoan['due_start_date'] = !empty($staffLoan['due_start_date'])
            ? date('d-m-Y', strtotime($staffLoan['due_start_date']))
            : '';

        $staffLoan['due_end_date'] = !empty($staffLoan['due_end_date'])
            ? date('d-m-Y', strtotime($staffLoan['due_end_date']))
            : '';

        $staffLoan['action'] = "
            <span class='icon-border_color staffLoanActionBtn'
                value='" . $staffLoan['id'] . "'
                data-id='" . $staffLoan['due_start_date'] . "'></span>
            <span class='icon-trash-2 staffLoanDeleteBtn'
                value='" . $staffLoan['id'] . "'
                data-id='" . $staffLoan['due_start_date'] . "'></span>
        ";

        $staff_Loan_list[] = $staffLoan;
    }
}

$pdo = null;

echo json_encode($staff_Loan_list);
?>
