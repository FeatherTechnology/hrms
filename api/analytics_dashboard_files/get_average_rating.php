<?php
// Get the average rating for the logged-in user's company.
// Calculates the average of all rating values and returns the result in '/5' format.

include '../../ajaxconfig.php';
session_start();

$user_id = $_SESSION['user_id'];

// Get Company ID
$getUser = $pdo->query("
    SELECT company_id
    FROM users
    WHERE id = '$user_id'
");
$userData = $getUser->fetch(PDO::FETCH_ASSOC);

$company_id = $userData['company_id'];

// Get Average Rating
$qry = $pdo->query("
    SELECT AVG(ra.rating_value) as avg_rating
    FROM rating_answers ra
    INNER JOIN rating_titles rt
        ON ra.rating_titles_id = rt.id
    WHERE rt.company_id = '$company_id'
");

$row = $qry->fetch(PDO::FETCH_ASSOC);

$avg_rating = !empty($row['avg_rating'])
    ? round($row['avg_rating'], 1)
    : 0;

echo $avg_rating . '/5';