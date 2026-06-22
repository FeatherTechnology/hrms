<?php
// to get the director creation list 
require '../../ajaxconfig.php';

$director_list_arr = [];

$stmt = $pdo->prepare(" SELECT dc.*, st.state_name AS state, dt.district_name AS district
    FROM director_creation dc
    LEFT JOIN districts dt ON dc.district = dt.id
    LEFT JOIN states st ON dc.state = st.id
");

$stmt->execute();

while ($DirectorInfo = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $DirectorInfo['action'] = "
        <span class='icon-border_color directorActionBtn'
              value='" . $DirectorInfo['id'] . "'>
        </span>
    ";

    $director_list_arr[] = $DirectorInfo;
}

$pdo = null;

echo json_encode($director_list_arr);
?>