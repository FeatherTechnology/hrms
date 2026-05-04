<?php
require '../../ajaxconfig.php';

$response = array();
$areaId = $_POST['aline_id'];
$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : null;
$qry = $pdo->query("
    (
        SELECT lnc.id AS line_id, lnc.linename
        FROM area_creation ac
        LEFT JOIN line_name_creation lnc ON ac.line_id = lnc.id
        LEFT JOIN area_creation_area_name acan ON ac.id = acan.area_creation_id
        WHERE acan.area_id = $areaId
    )
    UNION
    (
        SELECT lnc.id AS line_id, lnc.linename
        FROM line_name_creation lnc
        WHERE lnc.id = (SELECT line FROM customer_profile WHERE id = '$cus_profile_id')
    )
");


if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Close the connection
echo json_encode($response);
