<?php
require "../../ajaxconfig.php";

$user_creation_id = $_POST['id'];
$cmpy_id = $_POST['cmpy_id'];

if ($user_creation_id != '0' && $user_creation_id != '') {

    $stmt = $pdo->prepare("SELECT user_code FROM users WHERE id = ?");
    $stmt->execute([$user_creation_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_code_final = $row['user_code'];

} else {

    // Get company name
    $stmt = $pdo->prepare("SELECT company_name FROM company_creation WHERE id = ?");
    $stmt->execute([$cmpy_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    $company_name = $company['company_name'];

    // Create prefix: Feather Technology -> FTU
    $words = explode(' ', trim($company_name));
    $prefix = '';

    foreach ($words as $word) {
        $prefix .= strtoupper(substr($word, 0, 1));
    }

    $prefix .= 'U';

    // Find last code for this company prefix
    $stmt = $pdo->prepare("
        SELECT user_code
        FROM users
        WHERE user_code LIKE ?
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->execute([$prefix . '-%']);
    $lastUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastUser) {

        // FTU-101 -> 101
        $lastNumber = (int) substr($lastUser['user_code'], strlen($prefix) + 1);

        $newNumber = $lastNumber + 1;

    } else {

        $newNumber = 101;
    }

    $user_code_final = $prefix . '-' . $newNumber;
}

echo json_encode($user_code_final);