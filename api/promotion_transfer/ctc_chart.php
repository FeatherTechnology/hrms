
<?php
require "../../ajaxconfig.php";

header('Content-Type: application/json');

try {
    $staff_profile_id = isset($_POST['staff_profile_id'])
        ? trim($_POST['staff_profile_id'])
        : '';

    if ($staff_profile_id === '') {
        echo json_encode([
            "success" => false,
            "message" => "Staff profile ID is required",
            "columns" => [],
            "data" => []
        ]);
        exit;
    }

    // Get CTC component names, amounts, and total amount
    $sql = "SELECT
                sci.effective_from,
                sci.ctc_id,
                sci.ctc_amount,
                sci.total_amount,
                sci.total_ctc,
                cc.salary_component
            FROM staff_ctc_info sci
            LEFT JOIN ctc_creation cc
                ON cc.id = sci.ctc_id
            WHERE sci.staff_profile_id = :staff_profile_id
            ORDER BY sci.effective_from DESC, sci.ctc_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':staff_profile_id' => $staff_profile_id
    ]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $columns = [];
    $history = [];

    foreach ($result as $row) {
        $ctcId = $row['ctc_id'];
        $effectiveDate = $row['effective_from'];

        // Get component name
        $columns[$ctcId] = $row['salary_component']
            ?? ('CTC ' . $ctcId);

        // Create one record per effective date
        if (!isset($history[$effectiveDate])) {
            $history[$effectiveDate] = [
                'effective_from' => $effectiveDate,
                'ctc_amount' => $row['total_ctc'] ?? 0,
                'total_ctc' => $row['total_amount'] ?? 0
            ];
        }

        // Store component amount
        $history[$effectiveDate]['ctc_' . $ctcId] =
            $row['ctc_amount'] ?? 0;
    }

    // Fill missing component amounts with zero
    foreach ($history as &$record) {
        foreach ($columns as $ctcId => $ctcName) {
            $key = 'ctc_' . $ctcId;

            if (!isset($record[$key])) {
                $record[$key] = 0;
            }
        }
    }
    unset($record);

    // Format dynamic component columns
    $formattedColumns = [];

    foreach ($columns as $ctcId => $ctcName) {
        $formattedColumns[] = [
            "data" => "ctc_" . $ctcId,
            "title" => $ctcName
        ];
    }

    echo json_encode([
        "success" => true,
        "columns" => $formattedColumns,
        "data" => array_values($history)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "columns" => [],
        "data" => []
    ]);
}
?>
