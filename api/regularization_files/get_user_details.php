<?php
require "../../ajaxconfig.php";

session_start();

$userid = $_SESSION['user_id'] ?? '';

if (isset($_POST['user_id']) && $_POST['user_id'] != '') {
    $user_id  = $_POST['user_id'];
    $stf_con = "sc.id = :user_id";
} else {
    $user_id = $userid;
    $stf_con = "u.id = :user_id";
}

// $user_id =  isset($_POST['user_id']) ? $_POST['user_id'] : $userid;
$status = $_POST['status'];
$id = $_POST['id'];
if ($status == '0') {

    $query = "
            SELECT 
                sc.id,
                sc.staff_id,
                sc.staff_name,
                cc.company_name,
                cc.id as cmpy_id,
                bc.id as branch_id,
                bc.branch_name,
                depcr.id as dep_id,
                depcr.department_name,
                descr.id as des_id,
                descr.designation,
                tnc.id as team_id,
                tnc.team_name

            FROM staff_creation sc

            LEFT JOIN occupation_info oc 
                ON oc.id = (
                    SELECT MAX(id)
                    FROM occupation_info
                    WHERE staff_profile_id = sc.id
                )

            LEFT JOIN company_creation cc 
                ON cc.id = sc.company_id

            LEFT JOIN branch_creation bc 
                ON bc.id = oc.branch_id

            LEFT JOIN department_creation depcr 
                ON depcr.id = oc.department

            LEFT JOIN designation_creation descr 
                ON descr.id = oc.designation

            LEFT JOIN team_name_creation tnc 
                ON tnc.id = oc.team 
            
            LEFT JOIN users u 
                ON u.staff_name_id = sc.id

            WHERE $stf_con
            ";
            $stmt = $pdo->prepare($query);

$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}else{
    $query = "
            SELECT 
                sc.id,
                sc.staff_id,
                sc.staff_name,
                cc.company_name,
                cc.id as cmpy_id,
                bc.id as branch_id,
                bc.branch_name,
                depcr.id as dep_id,
                depcr.department_name,
                descr.id as des_id,
                descr.designation,
                tnc.id as team_id,
                tnc.team_name,
                reg.*

            FROM regularization reg
            LEFT JOIN staff_creation sc ON sc.id = reg.staff_profile_id

            LEFT JOIN company_creation cc 
                ON cc.id = reg.company_id

            LEFT JOIN branch_creation bc 
                ON bc.id = reg.branch_id

            LEFT JOIN department_creation depcr 
                ON depcr.id = reg.dep_id

            LEFT JOIN designation_creation descr 
                ON descr.id = reg.des_id

            LEFT JOIN team_name_creation tnc 
                ON tnc.id = reg.team_id 

            WHERE reg.id = :id
            ";

            $stmt = $pdo->prepare($query);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
}




$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($result);
