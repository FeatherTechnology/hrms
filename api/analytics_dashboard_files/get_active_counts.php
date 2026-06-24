<?php
// Get active Feedback, Rating, and Poll counts where status is active and the end date has not expired.

include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'];

$type = $_POST['type'] ?? '';


// GET USER DETAILS
$getUser = $pdo->query("
    SELECT 
        user_type,
        director_company,
        staff_name_id
    FROM users
    WHERE id = '$user_id'
");

$userData = $getUser->fetch(PDO::FETCH_ASSOC);


$user_type = $userData['user_type'];


// COMPANY CONDITION
if ($user_type == 1) {

    // DIRECTOR
    // Example: director_company = 1,2,3

    $director_company = $userData['director_company'];

    $company_condition = " company_id IN ($director_company) ";


} else {


    // STAFF
    // Get current company from occupation_info

    $staff_name_id = $userData['staff_name_id'];


    $occQry = $pdo->query("
        SELECT company_id
        FROM occupation_info
        WHERE id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = '$staff_name_id'
        )
    ");


    $occData = $occQry->fetch(PDO::FETCH_ASSOC);


    $company_id = $occData['company_id'];


    $company_condition = " company_id = '$company_id' ";

}



$currentDateTime = date('Y-m-d H:i:s');

// SET TABLE & STATUS COLUMN
$table = '';
$status_column = '';

if ($type == 'feedback') {
    $table = 'feedback_titles';
    $status_column = 'feedback_status';
} elseif ($type == 'poll') {
    $table = 'poll_titles';
    $status_column = 'poll_status';
} elseif ($type == 'rating') {
    $table = 'rating_titles';
    $status_column = 'rating_status';
} else {
    echo 0;
    exit;
}

// GET COUNT

$stmt = $pdo->query("
    SELECT COUNT(*) as total
    FROM $table
    WHERE 
        $company_condition
        AND $status_column = 0
        AND '$currentDateTime' <= end_date_time
");

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['total'];

$pdo = null;

?>