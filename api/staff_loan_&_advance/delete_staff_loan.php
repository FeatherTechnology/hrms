<!-- to delet the staff loan -->
<?php
require '../../ajaxconfig.php';

@session_start();

$id = $_POST['id'] ?? '';

$result = 0;

if (!empty($id)) {

    try {
        $stmt = $pdo->prepare("  DELETE FROM `staff_loan` WHERE id = ?");
        $qry = $stmt->execute([$id]);
        if ($qry && $stmt->rowCount() > 0) {
            $result = 1;
        }
    } catch (PDOException $e) {
        $result = 0;
    }
}

$pdo = null;
echo json_encode($result);
?>
