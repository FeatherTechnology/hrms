<?php
//Based user Mapping Info the Loan category has to show.
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$result = array();
$qry = $pdo->query("SELECT lcc.id, lc.loan_category, lcc.loan_limit FROM loan_category_creation lcc LEFT JOIN loan_category lc ON lcc.loan_category = lc.id JOIN users u ON FIND_IN_SET(lcc.id, u.loan_category) WHERE u.id ='$user_id' ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
