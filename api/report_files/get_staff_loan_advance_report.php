
<?php
require '../../ajaxconfig.php';
@session_start();

$company_id = $_POST['company_id'] ?? '';
$branch_id  = $_POST['branch_id'] ?? '';
$status     = $_POST['status'] ?? '';

$status = (int)$status;

$staff_Loan_list = [];

// Fetch loans based on company and optional branch
$sql = "
    SELECT
        sl.*,
        cc.company_name,
        sc.staff_name,
        bc.branch_name
    FROM staff_loan sl
    LEFT JOIN company_creation cc ON cc.id = sl.company_id
    LEFT JOIN staff_creation sc ON sc.id = sl.staff_id
    LEFT JOIN occupation_info oi ON oi.id = ( SELECT MAX(id)  FROM occupation_info  WHERE staff_profile_id = sc.id AND effective_from <= NOW() )
    LEFT JOIN branch_creation bc  ON bc.id = oi.branch_id
    WHERE sl.company_id = ?
";

$params = [$company_id];

// Branch filter (optional)
if ($branch_id !== '') {
    $sql .= " AND sl.branch_id = ?";
    $params[] = $branch_id;
}

$sql .= " ORDER BY sl.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch deduction records
$deductionStmt = $pdo->prepare("
    SELECT deduction_month, deduction_amount
    FROM staff_deduction
    WHERE staff_loan_id = ?
      AND deduction_month < DATE_FORMAT(
          DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
          '%Y-%m-01'
      )
    ORDER BY deduction_month ASC
");

$currentMonth = date('Y-m');

// Calculate loan balance
foreach ($loans as $staffLoan) {

    $totalPayable = (float)$staffLoan['loan_amount']
        + (float)$staffLoan['total_intrest'];

    $balance = $totalPayable;
    $deductions = [];

    // Get deductions for current loan
    $deductionStmt->execute([$staffLoan['id']]);

    while ($deduction = $deductionStmt->fetch(PDO::FETCH_ASSOC)) {

        $month = date(
            'Y-m',
            strtotime($deduction['deduction_month'])
        );

        $deductions[$month] = (float)$deduction['deduction_amount'];
    }

    // Calculate month-wise deductions
    if (!empty($staffLoan['due_start_date'])) {

        $startMonth = new DateTime(
            date('Y-m-01', strtotime($staffLoan['due_start_date']))
        );

        $currentDate = new DateTime(date('Y-m-01'));

        while ($startMonth <= $currentDate && $balance > 0) {

            $month = $startMonth->format('Y-m');

            // Previous months
            if ($month < $currentMonth) {

                if (array_key_exists($month, $deductions)) {
                    $amount = $deductions[$month];
                } else {
                    $amount = (float)$staffLoan['due_amount'];
                }

                $balance = max(0, $balance - $amount);

            }
            // Current month
            elseif ($month === $currentMonth) {

                if (array_key_exists($month, $deductions)) {
                    $balance = max(
                        0,
                        $balance - $deductions[$month]
                    );
                }
            }

            $startMonth->modify('+1 month');
        }
    }

    // STATUS FILTER
    // Status 1: Outstanding loans (balance > 0)
    if ($status == 1 && $balance <= 0) {
        continue;
    }

    // Status 2: Completed loans (balance <= 0)
    if ($status == 2 && $balance > 0) {
        continue;
    }

    // Calculate total deducted and remaining balance
    $staffLoan['total_deducted'] = round(
        $totalPayable - $balance,
        2
    );

    $staffLoan['balance_amount'] = round($balance, 2);

    // Format due dates
    $staffLoan['due_start_date'] = !empty($staffLoan['due_start_date'])
        ? date('d-m-Y', strtotime($staffLoan['due_start_date']))
        : '';

    $staffLoan['due_end_date'] = !empty($staffLoan['due_end_date'])
        ? date('d-m-Y', strtotime($staffLoan['due_end_date']))
        : '';

    // Add to final array
    $staff_Loan_list[] = $staffLoan;
}

// Close database connection
$pdo = null;

// Return JSON response
echo json_encode($staff_Loan_list);

?>
