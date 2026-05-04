<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$cus_profile_id = $_POST['cus_profile_id'];

try {
    $qry = $pdo->query("SELECT * FROM kyc_info WHERE cus_profile_id = '$cus_profile_id' ");
    if ($qry->rowCount() == 1 && $cus_profile_id !='') { //If Only one count of kyc for the customer then restrict to delete.
        $result = '0';
    } else {
        $qry = $pdo->prepare("SELECT upload FROM kyc_info WHERE id = :id");
        $qry->bindParam(':id', $id, PDO::PARAM_INT);
        if ($qry->execute()) {
            $row = $qry->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $filePath = "../../uploads/loan_entry/kyc/" . $row['upload'];
                if (is_file($filePath)) {
                    unlink($filePath);
                }

                $deleteQry = $pdo->prepare("DELETE FROM kyc_info WHERE id = :id");
                $deleteQry->bindParam(':id', $id, PDO::PARAM_INT);
                if ($deleteQry->execute()) {
                    $result = '1'; // Success
                }
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $result = '2'; // Failure
}

$pdo = null; // Close Connection

echo json_encode($result);