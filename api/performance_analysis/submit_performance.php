<?php
require '../../ajaxconfig.php';
@session_start();

$company_id         = $_POST['company_name'] ?? '';
$criteria           = $_POST['criteria'] ?? '';
$target_performance = $_POST['target_performance'] ?? '';
$weightage          = (int)($_POST['weightage'] ?? 0);
$effective_from     = $_POST['effective_from'] ?? '';
$user_id            = $_SESSION['userid'] ?? ''; // Double check your session key names ('userid' vs 'user_id')
$performance_id     = $_POST['performance_id'] ?? '';

$result = 0; 

if (!empty($company_id)) {
    
    // 1. Calculate the current total weightage for this company
    // If updating, exclude the current row's weightage from the sum calculation
    if (!empty($performance_id)) {
        $weightStmt = $pdo->prepare("SELECT SUM(CAST(weightage AS UNSIGNED)) FROM performance_analysis WHERE company_id = ? AND id != ? and status = 0");
        $weightStmt->execute([$company_id, $performance_id]);
    } else {
        $weightStmt = $pdo->prepare("SELECT SUM(CAST(weightage AS UNSIGNED)) FROM performance_analysis WHERE company_id = ? and status = 0");
        $weightStmt->execute([$company_id]);
    }
    
    $current_total_weightage = (int)$weightStmt->fetchColumn();

    // 2. Validation check: Will this entry push total weightage over 100?
    if (($current_total_weightage + $weightage) > 100) {
        $result = 3; // Code 3 indicates weightage limit exceeded
    } else {
        // 3. Process execution if validation passes
        if (!empty($performance_id)) {
            // UPDATE query converted to Prepared Statement
            $qry = $pdo->prepare("UPDATE `performance_analysis` SET `company_id` = ?, `criteria` = ?, `target_perform` = ?, `weightage` = ?, `effective_from` = ?, `update_login_id` = ?, `updated_on` = NOW() WHERE `id` = ?");
            if ($qry->execute([$company_id, $criteria, $target_performance, $weightage, $effective_from, $user_id, $performance_id])) {
                $result = 1; 
            }
        } else {
            // INSERT query converted to Prepared Statement
            $qry = $pdo->prepare("INSERT INTO `performance_analysis` (`company_id`, `criteria`, `target_perform`, `weightage`, `effective_from`, `insert_login_id`, `created_on`) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            if ($qry->execute([$company_id, $criteria, $target_performance, $weightage, $effective_from, $user_id])) {
                $result = 2; 
            }
        }
    }
}

echo json_encode($result);
$pdo = null;
?>