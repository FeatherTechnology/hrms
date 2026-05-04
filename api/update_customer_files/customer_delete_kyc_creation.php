<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$cus_id = $_POST['cus_id'];
$cus_profile_id=$_POST['cus_profile_id'];
try {
    // Fetch all unique cus_profile_id associated with the given cus_id from kyc_info
    $profileIdsQry = $pdo->prepare("
        SELECT DISTINCT kyc_info.cus_profile_id
        FROM kyc_info
        JOIN customer_profile ON customer_profile.id = kyc_info.cus_profile_id
        WHERE customer_profile.cus_id = :cus_id
    ");
    $profileIdsQry->bindParam(':cus_id', $cus_id, PDO::PARAM_STR);
    $profileIdsQry->execute();
    $profileIds = $profileIdsQry->fetchAll(PDO::FETCH_COLUMN);

    $result = '0'; // Default to not allowing deletion

    foreach ($profileIds as $cus_profile_id) {
        // Fetch the number of KYC records for each cus_profile_id
        $countQry = $pdo->prepare("SELECT COUNT(*) FROM kyc_info WHERE cus_profile_id = :cus_profile_id");
        $countQry->bindParam(':cus_profile_id', $cus_profile_id, PDO::PARAM_INT);
        $countQry->execute();
        $kycCount = $countQry->fetchColumn();

        // Allow deletion only if there is more than one KYC record for the cus_profile_id
        if ($kycCount > 1) {
            // Fetch the upload filename for the given KYC record ID
            $fetchQry = $pdo->prepare("SELECT upload FROM kyc_info WHERE id = :id");
            $fetchQry->bindParam(':id', $id, PDO::PARAM_INT);
            $fetchQry->execute();
            $row = $fetchQry->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // If the file exists, delete it
                $filePath = "../../uploads/loan_entry/kyc/" . $row['upload'];
                if (is_file($filePath)) {
                    unlink($filePath);
                }

                // Delete the KYC record from the database
                $deleteQry = $pdo->prepare("DELETE FROM kyc_info WHERE id = :id");
                $deleteQry->bindParam(':id', $id, PDO::PARAM_INT);
                if ($deleteQry->execute()) {
                    $result = '1'; // Success
                } else {
                    $result = '2'; // Failure to delete record
                }
            } else {
                $result = '2'; // Record not found
            }

            // Break out of the loop as the deletion is successful
            break;
        }
    }

    echo json_encode($result);
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    $result = '2'; // General failure
    echo json_encode($result);
}

$pdo = null; // Close Connection
?>
