
<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT bc.*, cc.company_name FROM `branch_creation` bc JOIN company_creation cc ON bc.company_id = cc.id WHERE bc.id='$id'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);