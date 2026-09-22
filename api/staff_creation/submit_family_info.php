<?php

require '../../ajaxconfig.php';
@session_start();

$staff_id = $_POST['staff_id'] ?? '';
$staff_profile_id = $_POST['staff_profile_id'] ?? '';
$fam_name = $_POST['fam_name'] ?? '';
$fam_relationship = $_POST['fam_relationship'] ?? '';
$fam_dob = !empty($_POST['fam_dob']) ? $_POST['fam_dob'] : null;
$fam_age = !empty($_POST['fam_age']) ? (int)$_POST['fam_age'] : null;
$fam_mem_sts = !empty($_POST['fam_mem_sts']) ? (int)$_POST['fam_mem_sts'] : null;
$fam_occupation = $_POST['fam_occupation'] ?? '';
$fam_mobile = $_POST['fam_mobile'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$family_id = $_POST['family_id'] ?? '';

$result = 0;

try {

    if ($family_id != '') {

        // Update existing family information
        $sql = "UPDATE `family_info` SET
                    `staff_id` = :staff_id,
                    `staff_profile_id` = :staff_profile_id,
                    `fam_name` = :fam_name,
                    `fam_relationship` = :fam_relationship,
                    `fam_dob` = :fam_dob,
                    `fam_age` = :fam_age,
                    `fam_mem_sts` = :fam_mem_sts,
                    `fam_occupation` = :fam_occupation,
                    `fam_mobile` = :fam_mobile,
                    `update_login_id` = :update_login_id,
                    `updated_on` = NOW()
                WHERE `id` = :family_id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':staff_id' => $staff_id,
            ':staff_profile_id' => $staff_profile_id,
            ':fam_name' => $fam_name,
            ':fam_relationship' => $fam_relationship,
            ':fam_dob' => $fam_dob,
            ':fam_age' => $fam_age,
            ':fam_mem_sts' => $fam_mem_sts,
            ':fam_occupation' => $fam_occupation,
            ':fam_mobile' => $fam_mobile,
            ':update_login_id' => $user_id,
            ':family_id' => $family_id
        ]);

        $result = 1; // Update successful

    } else {

        // Insert new family information
        $sql = "INSERT INTO `family_info`
                    (
                        `staff_id`,
                        `staff_profile_id`,
                        `fam_name`,
                        `fam_relationship`,
                        `fam_dob`,
                        `fam_age`,
                        `fam_mem_sts`,
                        `fam_occupation`,
                        `fam_mobile`,
                        `insert_login_id`,
                        `created_on`
                    )
                VALUES
                    (
                        :staff_id,
                        :staff_profile_id,
                        :fam_name,
                        :fam_relationship,
                        :fam_dob,
                        :fam_age,
                        :fam_mem_sts,
                        :fam_occupation,
                        :fam_mobile,
                        :insert_login_id,
                        NOW()
                    )";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':staff_id' => $staff_id,
            ':staff_profile_id' => $staff_profile_id,
            ':fam_name' => $fam_name,
            ':fam_relationship' => $fam_relationship,
            ':fam_dob' => $fam_dob,
            ':fam_age' => $fam_age,
            ':fam_mem_sts' => $fam_mem_sts,
            ':fam_occupation' => $fam_occupation,
            ':fam_mobile' => $fam_mobile,
            ':insert_login_id' => $user_id
        ]);

        $result = 2; // Insert successful
    }
} catch (PDOException $e) {

    $result = 0;

    // For debugging during development
    // error_log($e->getMessage());
}

echo json_encode($result);
