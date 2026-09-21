<!-- to get the staff loan outer list -->
<?php
require '../../ajaxconfig.php';
@session_start();

$staff_Loan_list = [];

$user_id = $_SESSION['user_id'];

// Get logged-in user details
$userStmt = $pdo->prepare("
    SELECT user_type, director_company, company_id
    FROM `users`
    WHERE id = ?
");

$userStmt->execute([$user_id]);

$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    $user_type = $user['user_type'];

    if ($user_type == 1) {

        // Director
        // director_company contains values like: 1,2,3,4

        $director_company = $user['director_company'];

        if (!empty($director_company)) {

            $company_ids = array_filter(
                array_map('trim', explode(',', $director_company))
            );

            $placeholders = implode(',', array_fill(0, count($company_ids), '?'));

            $stmt = $pdo->prepare("
                SELECT 
                    sl.*,
                    cc.company_name,
                    sc.staff_name
                FROM `staff_loan` sl
                LEFT JOIN `company_creation` cc
                    ON cc.id = sl.company_id
                LEFT JOIN `staff_creation` sc
                    ON sc.id = sl.staff_id
                WHERE sl.company_id IN ($placeholders) AND sl.due_end_date >= CURDATE()
                ORDER BY sl.id DESC
            ");

            $stmt->execute($company_ids);

        }

    } elseif ($user_type == 2) {

        // Staff
        // Take company_id from user table

        $company_id = $user['company_id'];

        $stmt = $pdo->prepare("
            SELECT 
                sl.*,
                cc.company_name,
                sc.staff_name
            FROM `staff_loan` sl
            LEFT JOIN `company_creation` cc
                ON cc.id = sl.company_id
            LEFT JOIN `staff_creation` sc
                ON sc.id = sl.staff_id
            WHERE sl.company_id = ? AND sl.due_end_date >= CURDATE()
            ORDER BY sl.id DESC
        ");

        $stmt->execute([$company_id]);
    }

    if (isset($stmt) && $stmt->rowCount() > 0) {

        while ($staffLoan = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $staffLoan['due_start_date'] = date(
                    'd-m-Y',
                    strtotime($staffLoan['due_start_date'])
                );

            $staffLoan['due_end_date'] = date(
                    'd-m-Y',
                    strtotime($staffLoan['due_end_date'])
                );

            $staffLoan['action'] = "
                <span class='icon-border_color staffLoanActionBtn' value='" . $staffLoan['id'] . "'></span>
                <span class='icon-trash-2 staffLoanDeleteBtn' value='" . $staffLoan['id'] . "' data-id='".$staffLoan['due_start_date']."'></span>
            ";
        

            $staff_Loan_list[] = $staffLoan;
        }
    }
}

$pdo = null;

echo json_encode($staff_Loan_list);

?>