<!-- to get the salary advance outer list -->

<?php

require '../../ajaxconfig.php';
@session_start();

$staff_salary_advance_list = [];

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
        // director_company contains values like: 1,2,3

        $director_company = $user['director_company'];

        if (!empty($director_company)) {

            $company_ids = array_filter(
                array_map('trim', explode(',', $director_company))
            );

            $placeholders = implode(
                ',',
                array_fill(0, count($company_ids), '?')
            );

            $stmt = $pdo->prepare("
                SELECT 
                    sa.*,
                    cc.company_name,
                    sc.staff_name
                FROM `staff_salary_adavance` sa
                LEFT JOIN `company_creation` cc
                    ON cc.id = sa.company_id
                LEFT JOIN `staff_creation` sc
                    ON sc.id = sa.staff_id
                WHERE sa.company_id IN ($placeholders) AND sa.dedection_month >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                ORDER BY sa.id DESC
            ");

            $stmt->execute($company_ids);
        }

    } elseif ($user_type == 2) {

        // Staff
        // Take company_id from users table

        $company_id = $user['company_id'];

        $stmt = $pdo->prepare("
            SELECT 
                sa.*,
                cc.company_name,
                sc.staff_name
            FROM `staff_salary_adavance` sa
            LEFT JOIN `company_creation` cc
                ON cc.id = sa.company_id
            LEFT JOIN `staff_creation` sc
                ON sc.id = sa.staff_id
            WHERE sa.company_id = ? AND sa.dedection_month >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
            ORDER BY sa.id DESC
        ");

        $stmt->execute([$company_id]);
    }

    if (isset($stmt)) {

        while ($staffAdvance = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // Format deduction month
          if (!empty($staffAdvance['dedection_month'])) {
            $staffAdvance['dedection_month'] = date(
                'M - Y',
                strtotime($staffAdvance['dedection_month'])
            );
        }

            // Edit and Delete buttons
            $staffAdvance['action'] = "
                <span 
                    class='icon-border_color staffSalaryAdvanceActionBtn'
                    value='" . $staffAdvance['id'] . "'>
                </span>

                <span 
                    class='icon-trash-2 staffSalaryAdvanceDeleteBtn'
                    value='" . $staffAdvance['id'] . "'>
                </span>
            ";

            $staff_salary_advance_list[] = $staffAdvance;
        }
    }
}

$pdo = null;

echo json_encode($staff_salary_advance_list);

?>
