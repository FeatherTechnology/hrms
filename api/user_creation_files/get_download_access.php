<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

// Sanitize the user ID to prevent SQL injection
$user_id = intval($user_id);

// Construct the SQL query
$query = "SELECT download_access FROM `users` WHERE id = $user_id";

// Execute the query
$result = $pdo->query($query);

// Fetch result and prepare the response
if ($result->rowCount() > 0) {
    $data = $result->fetch(PDO::FETCH_ASSOC);
    $response = ['download_access' => (int)$data['download_access']];
} else {
    $response = ['download_access' => 0]; // Default to no access if user not found
}

// Close connection
$pdo = null;

// Return JSON response
echo json_encode($response);
