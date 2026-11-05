<?php
require '../../ajaxconfig.php';
$id = (int)($_POST['id'] ?? 0);

$result = [];

try {
    // Master Details
    $stmt = $pdo->prepare("
        SELECT *
        FROM target_setup
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode([]);
        exit;
    }

    // Staff Target Details
   $stmt = $pdo->prepare("
    SELECT
        tsd.id,
        tsd.staff_id,
        sp.staff_name,
        tsd.incentive_titles,
        tsd.target_counts,
        tsd.target_amounts,
        tsd.minimum_counts,
        tsd.minimum_amounts,
        tsd.payout_types,
        tsd.ctc_percentages,
        tsd.fixed_amounts,
        tsd.achievement_basis,
        tsd.achieved_value, 
        tsd.incentive_amount,
         tsd.final_incentive,
         oi.total_ctc
    FROM target_setup_details tsd
    LEFT JOIN staff_creation sp ON sp.id = tsd.staff_id
    LEFT JOIN occupation_info oi ON oi.id = ( SELECT MAX(id)  FROM occupation_info  WHERE staff_profile_id = sp.id AND effective_from <= NOW() )
    WHERE tsd.target_setup_id = ?
    ORDER BY tsd.id ASC
");
$stmt->execute([$id]);

$result['staff_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode([
        'error' => 1,
        'message' => $e->getMessage()
    ]);
}

$pdo = null;
?>