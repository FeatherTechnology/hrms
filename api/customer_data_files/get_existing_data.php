<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : '';
$existing_detail = isset($_POST['coll_data']) ? $_POST['coll_data'] : '';

$result = 0;

$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1, cs.status as c_sts ,cs.sub_status as c_substs FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
LEFT JOIN area_name_creation anc ON cp.area = anc.id 
LEFT JOIN area_creation ac ON cp.line = ac.line_id 
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id 
INNER JOIN (SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id) latest ON cp.id = latest.max_id  WHERE cp.cus_id='$cus_id' ORDER BY cp.id DESC LIMIT 1");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $cus_name = $row['cus_name'];
        $area = $row['area'];
        $mobile1 = $row['mobile1'];
        $linename = $row['linename'];
        $branch_name = $row['branch_name'];
        $c_sts = $row['c_sts'];
        $c_substs = $row['c_substs'];
        $cus_profile_id = $row['id'];
        $qry1 = $pdo->query("INSERT INTO `existing_customer`(`cus_id`, `cus_profile_id`,`cus_name`, `area`, `mobile1`, `linename`,`branch_name`, `c_sts`, `c_substs`,`existing_detail`, `insert_login_id`, `created_on` ) VALUES ('$cus_id','$cus_profile_id','$cus_name','$area','$mobile1','linename','$branch_name','$c_sts','$c_substs','$existing_detail','$user_id',CURRENT_TIMESTAMP())");

        $result = 1; // Insert successful
    }
}

echo json_encode($result);
