<?php
require '../../ajaxconfig.php';


$new_promo_arr = array();
$i=0;
$qry = $pdo->query("SELECT id,cus_name,area,mobile,loan_cat,loan_amount FROM customer_data ");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
  
    $row['action'] = "<span class='icon-delete newPromoDeleteBtn' value='" . $row['id'] . "'></span>";

        $new_promo_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($new_promo_arr);
$pdo = null; // Close Connection
?>