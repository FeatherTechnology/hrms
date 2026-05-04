<?php
//  1. Set the log file path
$logFile = __DIR__ . '/monthly_update_log.txt';

// 2. Define the logMessage() function
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// 3. Prevent script from running on non-1st days
if (date('d') !== '1') {
    logMessage("Not the 1st of the month. Script exited.");
    exit;
}
logMessage(" Script started at " . date('h:i:s A'));

include_once(__DIR__ . '/../../ajaxconfig.php');

logMessage("Database connection loaded. Fetching customer profile IDs...");

try {
    $qry = $pdo->query("SELECT cs.cus_profile_id as cp_id FROM customer_status cs WHERE cs.status = 7 ORDER BY cs.id ASC");
    $customer_profile_id = array_column($qry->fetchAll(PDO::FETCH_ASSOC), 'cp_id');
} catch (Exception $e) {
    logMessage(" Database error: " . $e->getMessage());
    exit;
}

logMessage("Total cp_ids fetched: " . count($customer_profile_id));

$chunks = array_chunk($customer_profile_id, 2);

foreach ($chunks as $chunk) {
    foreach ($chunk as $cp_id) {
        logMessage("Processing cp_id: $cp_id");

        $postData = ['cpID' => $cp_id];

        $ch = curl_init('http://marudhamcapitals-001-site5.ntempurl.com/api/collection_files/resetCustomerStatus.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $responseJSON = curl_exec($ch);

        if (curl_errno($ch)) {
            logMessage(" CURL error for resetCustomerStatus: " . curl_error($ch));
            curl_close($ch);
            continue;
        }

        curl_close($ch);
        $response = json_decode($responseJSON, true);

        if (empty($response) || !isset($response['cp_id'])) {
            logMessage(" No response for cp_id $cp_id.");
            continue;
        }

        $follow_cus_sts = $response['follow_cus_sts'];

        $ch2 = curl_init('http://marudhamcapitals-001-site5.ntempurl.com/api/collection_files/updateCustomerStatus.php');
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, [
            'cp_id' => $cp_id,
            'follow_cus_sts' => $follow_cus_sts,
            'userid' => '1'
        ]);
        $updateResponse = curl_exec($ch2);

        if (curl_errno($ch2)) {
            logMessage(" CURL error for updateCustomerStatus: " . curl_error($ch2));
            curl_close($ch2);
            continue;
        }

        curl_close($ch2);
        logMessage(" Updated cp_id $cp_id: $updateResponse");
    }
}

logMessage("Script finished.");
