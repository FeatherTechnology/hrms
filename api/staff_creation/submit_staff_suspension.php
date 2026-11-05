
<?php
require '../../ajaxconfig.php';
@session_start();

$staff_creation_id = $_POST['staff_creation_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$reason = $_POST['reason'];

$user_id = $_SESSION['user_id'];
$suspension_id = $_POST['suspension_id'] ?? '';

$result = 0;

// Check whether the selected dates are overlapping
$check = $pdo->prepare("
    SELECT COUNT(*)
    FROM staff_suspended
    WHERE staff_profile_id = :staff_id
    AND from_date <= :to_date
    AND to_date >= :from_date
    AND id != :suspension_id
");

$check->execute([
    ':staff_id' => $staff_creation_id,
    ':from_date' => $from_date,
    ':to_date' => $to_date,
    ':suspension_id' => $suspension_id != '' ? $suspension_id : 0
]);

$overlap_count = $check->fetchColumn();

if ($overlap_count > 0) {

    $result = 3; // Overlapping dates

} else {

    // Update existing suspension
    if ($suspension_id != '') {

        $qry = $pdo->prepare("
            UPDATE staff_suspended SET
                staff_profile_id = :staff_id,
                from_date = :from_date,
                to_date = :to_date,
                reason = :reason,
                update_login_id = :user_id,
                updated_date = NOW()
            WHERE id = :id
        ");

        if ($qry->execute([
            ':staff_id' => $staff_creation_id,
            ':from_date' => $from_date,
            ':to_date' => $to_date,
            ':reason' => $reason,
            ':user_id' => $user_id,
            ':id' => $suspension_id
        ])) {
            $result = 1; // Update successful
        }

    } else {

        // Insert new suspension
        $qry = $pdo->prepare("
            INSERT INTO staff_suspended
            (
                staff_profile_id,
                from_date,
                to_date,
                reason,
                insert_login_id,
                created_date
            )
            VALUES
            (
                :staff_id,
                :from_date,
                :to_date,
                :reason,
                :user_id,
                NOW()
            )
        ");

        if ($qry->execute([
            ':staff_id' => $staff_creation_id,
            ':from_date' => $from_date,
            ':to_date' => $to_date,
            ':reason' => $reason,
            ':user_id' => $user_id
        ])) {
            $result = 2; // Insert successful
        }
    }
}

echo json_encode($result);

$pdo = null;
?>
