<?php

require '../../ajaxconfig.php';

@session_start();

$company_name   = $_POST['company_name'];
$permission_type   = $_POST['permission_type'];
$max_permission = $_POST['max_permission']; 
$dep_name       = $_POST['dep_name'];
$permission_id       = $_POST['permission_id'];
$effective_date = $_POST['effective_date'] . '-01';

$user_id        = $_SESSION['user_id'];

$result = 0;

if ($permission_id !='') {

    $qry = $pdo->query("UPDATE company_policies SET permission_type ='$permission_type', max_permission = '$max_permission',effective_date = '$effective_date',  update_login_id = '$user_id', updated_date = NOW() 
    WHERE id = '$permission_id'");


    if ($qry) {
        $result = 1;
    }
 } else {

    // Check whether this department already has a policy for the selected month
    $check = $pdo->prepare("
        SELECT id
        FROM company_policies
        WHERE company_id = ?
          AND department_id = ?
          AND effective_date = ?
        LIMIT 1
    ");

    $check->execute([$company_name, $dep_name, $effective_date]);

    if ($check->fetch(PDO::FETCH_ASSOC)) {

        // Record already exists for this month
        $result = 3;

    } else {

        // Insert new policy
        $insert = $pdo->prepare("
            INSERT INTO company_policies
                (company_id, department_id, permission_type, max_permission,
                 effective_date, insert_login_id, created_date)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if ($insert->execute([
            $company_name,
            $dep_name,
            $permission_type,
            $max_permission,
            $effective_date,
            $user_id
        ])) {
            $result = 2;
        }
    }
}


echo json_encode($result);
