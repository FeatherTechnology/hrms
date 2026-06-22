<?php
// to submit the director creation 

require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$director_id = $_POST['director_id'];
$director_name = $_POST['director_name'];
$state = $_POST['state'];
$district = $_POST['district'];
$address = $_POST['address'];
$mobile_number = $_POST['mobile_number'];
$directorID = $_POST['directorID'];

$result = 0;
try {
    // Begin transaction
    $pdo->beginTransaction();
    // Get the latest Branch code
    if ($directorID != '0' && $directorID != '') {
        $qry = $pdo->query("UPDATE `director_creation` SET `director_name`='$director_name ',`state`='$state',`district`='$district',`address`='$address',`mobile_number`='$mobile_number',`update_login_id`='$user_id',`updated_date`= now() WHERE `id`='$directorID'");
        if ($qry) {
            $result = 1; //update
        }
    } else {

        // Get Last Director ID
        $qry = $pdo->query(" SELECT director_id FROM director_creation ORDER BY CAST(SUBSTRING_INDEX(director_id, '-', -1) AS UNSIGNED) DESC LIMIT 1 ");

        $row = $qry->fetch(PDO::FETCH_ASSOC);

        if (!empty($row['director_id'])) {

            // Example: D-109
            $last_id = $row['director_id'];

            $number = (int) substr($last_id, strpos($last_id, '-') + 1);

            $new_number = $number + 1;

            // D-110
            $director_id = 'D-' . $new_number;
        } else {

            // First Director ID
            $director_id = 'D-101';
        }

        $qry = $pdo->query("INSERT INTO `director_creation`( `director_id`, `director_name`, `state`, `district`,`address`, `mobile_number`, `insert_login_id`,`inserted_date`) VALUES ('$director_id','$director_name','$state','$district','$address','$mobile_number','$user_id',now())");

        if ($qry) {
            $result = 2; //Insert
        }
    } // Commit transaction
    $pdo->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
    exit;
}
$pdo = null;
echo json_encode($result);
