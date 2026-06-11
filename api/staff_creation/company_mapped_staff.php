<?php

include '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$dept_id    = $_POST['dept_id'];
 if (isset($_POST['status'])) {
    $status = $_POST['status'];
 } else {
    $status = '';
 }

$where = " WHERE 1 ";

if ($company_id != '') {
    $where .= " AND sc.company_id = '".$company_id."' ";
}

if ($status != '') {
    $where .= " AND sc.status = '".$status."' ";
}

if ($dept_id != '') {
    $where .= " AND oi.department = '".$dept_id."' ";
}

/*
    Latest occupation_info entry only
    MAX(id) used to get latest row
*/

$sql = "
    SELECT 
        sc.id,
        sc.staff_name
    FROM staff_creation sc

    INNER JOIN (
        SELECT *
        FROM occupation_info
        WHERE id IN (
            SELECT MAX(id)
            FROM occupation_info
            GROUP BY staff_profile_id
        )
    ) oi ON sc.id = oi.staff_profile_id

    $where

    ORDER BY sc.staff_name ASC
";

$qry = $pdo->query($sql);

$result = array();

if ($qry->rowCount() > 0) {

    $result = $qry->fetchAll(PDO::FETCH_ASSOC);

}

echo json_encode($result);

?>