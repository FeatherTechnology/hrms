<?php
// to get the auto increment id for the director
require "../../ajaxconfig.php";

$response = array();

$id = $_POST['id'];

if ($id != '0' && $id != '') {

    // Edit Mode
    $qry = $pdo->prepare("SELECT director_id FROM director_creation WHERE id = ?");
    $qry->execute([$id]);
    $row = $qry->fetch(PDO::FETCH_ASSOC);

    $auto_director_id = $row['director_id'];

} else {

    // Add Mode - Generate Next Director ID

    $qry = $pdo->query("
        SELECT director_id
        FROM director_creation
        ORDER BY CAST(SUBSTRING_INDEX(director_id, '-', -1) AS UNSIGNED) DESC
        LIMIT 1
    ");

    $row = $qry->fetch(PDO::FETCH_ASSOC);

    if (!empty($row['director_id'])) {

        // Example: D-109
        $last_id = $row['director_id'];

        $parts = explode('-', $last_id);
        $last_number = (int)$parts[1];

        $new_number = $last_number + 1;

        // Example: D-110
        $auto_director_id = 'D-' . $new_number;

    } else {

        // First Record
        $auto_director_id = 'D-101';
    }
}

$response['director_id'] = $auto_director_id;

echo json_encode($response);
?>