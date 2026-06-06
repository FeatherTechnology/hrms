<?php
require '../../ajaxconfig.php';
@session_start();


$rating_titles_id = $_POST['rating_titles_id'];
$rating_company_name = $_POST['rating_company_name'];
$rating_department_name   = isset($_POST['rating_department_name']) ? $_POST['rating_department_name'] : [];
$rating_department_name2  = isset($_POST['rating_department_name2']) ? explode(',', $_POST['rating_department_name2']) : [];
$rating_start_date = $_POST['rating_start_date'];
$rating_end_date = $_POST['rating_end_date'];
$rating_title = $_POST['rating_title'];
$rating_description = $_POST['rating_description'];
$rating_status = $_POST['rating_status'];
$user_id = $_SESSION['user_id'];

$result = 0;

if ($rating_titles_id != '') {

    $qry = $pdo->query("UPDATE `rating_titles` SET `company_id`='$rating_company_name', `start_date_time`='$rating_start_date', `end_date_time`='$rating_end_date',
    `rating_title`='$rating_title', `rating_description`='$rating_description', `rating_status`='$rating_status', `update_login_id`='$user_id', `updated_date`=now() WHERE `id`='$rating_titles_id'");

    // Calculate deleted and newly added IDs
    $rating_department_name = array_map('intval', $rating_department_name);

    $rating_to_delete = array_diff($rating_department_name2, $rating_department_name);
    $rating_to_insert = array_diff($rating_department_name, $rating_department_name2);

    // Delete unselected departments
    foreach ($rating_to_delete as $department_del_id) {
        $pdo->query("DELETE FROM rating_department_mapping WHERE rating_titles_id = $rating_titles_id AND department_id	 = $department_del_id");
    }

    // Insert new departments
    foreach ($rating_to_insert as $department_new_id) {
        $pdo->query("INSERT INTO rating_department_mapping (rating_titles_id, department_id	) VALUES ($rating_titles_id, $department_new_id)");
    }

    if ($pdo) {
        $result = 1; //update successful
    }
} else {

    $qry = $pdo->query("INSERT INTO `rating_titles`(`company_id`,`start_date_time`, `end_date_time`, `rating_title`, `rating_description`, `rating_status`, `insert_login_id`, `created_date`) VALUES ('$rating_company_name','$rating_start_date', '$rating_end_date', '$rating_title', '$rating_description', '$rating_status', '$user_id', now())");

    $rating_titles_id = $pdo->lastInsertId();

    // Insert into department mapping table
    foreach ($rating_department_name as $department_id) {
        $department_id     = (int)trim($department_id);
        if ($department_id > 0) {
            $departmentQry = "INSERT INTO rating_department_mapping (rating_titles_id, department_id	) VALUES ($rating_titles_id, $department_id	)";
            $pdo->query($departmentQry) or die("Error inserting department map: " . $pdo->errorInfo());
        }
    }

    if ($pdo) {
        $result = 2; //Insert successfull
    }
}

echo json_encode($result);
