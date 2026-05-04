<?php
require '../../ajaxconfig.php';
$qry = $pdo->query("SELECT lcc.id, lc.loan_category, lcc.loan_limit FROM loan_category_creation lcc LEFT JOIN loan_category lc ON lcc.loan_category = lc.id");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
