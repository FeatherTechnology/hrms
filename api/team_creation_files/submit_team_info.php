<?php

/** Team Save **
 * Purpose:
 * - Checks whether the team name already exists.
 * - Updates an existing team when team_id is provided.
 * - Inserts a new team when team_id is empty.
 * - Maintains created/updated user tracking.
 *
 * Return Values:
 * 0 = Failed
 * 1 = Update Successful
 * 2 = Insert Successful
 * 3 = Team Already Exists
 */

require '../../ajaxconfig.php';
@session_start();

$team_code = $_POST['team_code'];
$team_name = $_POST['team_name'];
$team_id   = $_POST['team_id'];
$company_id   = $_POST['company_id'];
$user_id   = $_SESSION['user_id'];

$result = 0;

/* Check Duplicate Team Name */
$stmt = $pdo->prepare("SELECT id
    FROM team_name_creation
    WHERE REPLACE(TRIM(team_name), ' ', '') = REPLACE(TRIM(?), ' ', '')
    AND team_status = 0 AND company_id = ?
");

$stmt->execute([$team_name,$company_id]);

if ($stmt->rowCount() > 0) {

    $result = 3; // Already Exists

} else {

    if (!empty($team_id)) {

        /* Update Team */
        $stmt = $pdo->prepare("UPDATE team_name_creation
            SET
                team_code = ?,
                team_name = ?,
                company_id = ?,
                update_login_id = ?,
                updated_date = NOW()
            WHERE id = ?
        ");

        $qry = $stmt->execute([
            $team_code,
            $team_name,
            $company_id,
            $user_id,
            $team_id
        ]);

        if ($qry) {
            $result = 1; // Update Successful
        }
    } else {

        /* Insert Team */
        $stmt = $pdo->prepare("INSERT INTO team_name_creation
            (
                team_code,
                team_name,
                company_id,
                insert_login_id,
                created_date
            )
            VALUES
            (
                ?, ?, ?,?, NOW()
            )
        ");

        $qry = $stmt->execute([
            $team_code,
            $team_name,
            $company_id,
            $user_id
        ]);

        if ($qry) {
            $result = 2; // Insert Successful
        }
    }
}

$pdo = null; // Close Connection

echo json_encode($result);
