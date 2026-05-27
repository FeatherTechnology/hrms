<?php
require "../../ajaxconfig.php";

$response = array();

$company_id = $_POST['company_id'];
$id         = $_POST['id'];

if ($id != '0' && $id != '') {

    // Edit Mode
    $qry = $pdo->query("SELECT staff_id FROM staff_creation WHERE staff_id = '$id'");
    $qry_info = $qry->fetch(PDO::FETCH_ASSOC);
    $auto_staff_id = $qry_info['staff_id'];

} else {

    // Get Company Name
    $qry = $pdo->query("SELECT company_name FROM company_creation WHERE id = '$company_id'");
    $row = $qry->fetch(PDO::FETCH_ASSOC);

    $company_name = trim($row['company_name']);

    // Remove extra spaces
    $company_name = preg_replace('/\s+/', ' ', $company_name);

    // Split words
    $words = explode(' ', $company_name);

    // Take first letter of all words
    $prefix = '';

    foreach ($words as $word) {
        if ($word != '') {
            $prefix .= strtoupper(substr($word, 0, 1));
        }
    }

    // Add S for Staff
    $prefix .= 'S';
    // Get Last Staff ID
    $qry = $pdo->query("SELECT MAX(staff_id) as staff_id 
                        FROM staff_creation 
                        WHERE company_id = '$company_id'");

    $row = $qry->fetch(PDO::FETCH_ASSOC);

    if ($row['staff_id'] != '') {

        $last_id = $row['staff_id'];
        $number = explode('-', $last_id)[1];

        $new_number = (int)$number + 1;

        $auto_staff_id = $prefix . '-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

    } else {

        $auto_staff_id = $prefix . '-001';
    }
}

$response['staff_id'] = $auto_staff_id;

echo json_encode($response);
?>