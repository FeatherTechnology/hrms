<?php
require '../../ajaxconfig.php';

$search_list_arr = array();

$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : '';
$aadhar_num = isset($_POST['aadhar_num']) ? $_POST['aadhar_num'] : '';
$cus_name = isset($_POST['cus_name']) ? $_POST['cus_name'] : '';
$area = isset($_POST['area']) ? $_POST['area'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

$searchCond = [];
$searchParam = [];

if (!empty($cus_id)) {
    $searchCond[] = "cus_id LIKE :cus_id";
    $searchParam[':cus_id'] = "%$cus_id%";
}
if (!empty($aadhar_num)) {
    $searchCond[] = "aadhar_num LIKE :aadhar_num";
    $searchParam[':aadhar_num'] = "%$aadhar_num%";
}
if (!empty($cus_name)) {
    $searchCond[] = "cus_name LIKE :cus_name";
    $searchParam[':cus_name'] = "%$cus_name%";
}
if (!empty($mobile)) {
    $searchCond[] = "mobile1 LIKE :mobile";
    $searchParam[':mobile'] = "%$mobile%";
}
if (!empty($area)) {
    $searchCond[] = "(area LIKE :area OR 
                      area IN (SELECT id FROM area_name_creation WHERE areaname LIKE :areaName))";
    $searchParam[':area'] = "%$area%";
    $searchParam[':areaName'] = "%$area%";
}


$whereSearch = "";
if (count($searchCond) > 0) {
    $whereSearch = " WHERE " . implode(" OR ", $searchCond);
}

$findCusId = $pdo->prepare("SELECT cus_id FROM customer_profile $whereSearch");
foreach ($searchParam as $key => $value) {
    $findCusId->bindValue($key, $value);
}
$findCusId->execute();
$cusIdList = $findCusId->fetchAll(PDO::FETCH_COLUMN);

// If nothing found → return empty result
if (empty($cusIdList)) {
    echo json_encode([]);
    exit;
}

// Keep only unique cus_id values
$cusIdList = array_unique($cusIdList);

$inClause = implode(",", array_fill(0, count($cusIdList), "?"));

$sql = "SELECT 
            cp.cus_id, cp.aadhar_num, cp.cus_name, 
            anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1
        FROM customer_profile cp
        LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
        LEFT JOIN area_name_creation anc ON cp.area = anc.id
        LEFT JOIN area_creation ac ON cp.line = ac.line_id
        LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
        INNER JOIN (
            SELECT cus_id, MAX(id) AS max_id
            FROM customer_profile
            WHERE cus_id IN ($inClause)
            GROUP BY cus_id
        ) latest ON cp.id = latest.max_id
        ORDER BY cp.id DESC";

$stmt = $pdo->prepare($sql);

$i = 1;
foreach ($cusIdList as $cid) {
    $stmt->bindValue($i, $cid, PDO::PARAM_STR);
    $i++;
}

$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>
                <a href='#' class='view_customer' value='" . $row['cus_id'] . "'>View</a>
            </div>
        </div>";

    $search_list_arr[] = $row;
}

echo json_encode($search_list_arr);
?>
