<?php

/** Location Access Save **
 * Purpose:
 * - Checks for overlapping location access periods for the selected staff.
 * - Updates an existing location access record when location_access_id is provided.
 * - Inserts a new location access record when location_access_id is empty.
 * - Stores assigned branch, location coordinates, and access reason.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Date Range Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$staff_id           = $_POST['staff_id'];
$staff_profile_id   = $_POST['staff_profile_id'];
$from_date          = $_POST['from_date'];
$to_date            = $_POST['to_date'];
$branch_name_three  = $_POST['branch_name_three'];
$branch_location    = $_POST['branch_location'];
$reason             = $_POST['reason'];
$location_access_id = $_POST['location_access_id'];
$user_id            = $_SESSION['user_id'];

$result = 0;

/* Check Overlapping Date Range */
$stmt = $pdo->prepare("SELECT id
    FROM location_access_mapping
    WHERE staff_profile_id = ?
    AND status = 0
    AND (
        ? BETWEEN from_date AND to_date
        OR
        ? BETWEEN from_date AND to_date
        OR
        from_date BETWEEN ? AND ?
        OR
        to_date BETWEEN ? AND ?
    )
    AND id != ?
");

$stmt->execute([
    $staff_profile_id,
    $from_date,
    $to_date,
    $from_date,
    $to_date,
    $from_date,
    $to_date,
    $location_access_id ?: 0
]);

if ($stmt->rowCount() > 0) {

    $result = 3; // Already Exists

} else {

    if (!empty($location_access_id)) {

        /* Update Location Access */
        $stmt = $pdo->prepare("UPDATE location_access_mapping
            SET
                staff_id = ?,
                staff_profile_id = ?,
                from_date = ?,
                to_date = ?,
                assigned_branch = ?,
                lattitude_longitude = ?,
                reason = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $staff_id,
            $staff_profile_id,
            $from_date,
            $to_date,
            $branch_name_three,
            $branch_location,
            $reason,
            $user_id,
            $location_access_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }
    } else {

        /* Insert Location Access */
        $stmt = $pdo->prepare("INSERT INTO location_access_mapping
            (
                staff_id,
                staff_profile_id,
                from_date,
                to_date,
                assigned_branch,
                lattitude_longitude,
                reason,
                insert_login_id
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $qry = $stmt->execute([
            $staff_id,
            $staff_profile_id,
            $from_date,
            $to_date,
            $branch_name_three,
            $branch_location,
            $reason,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
