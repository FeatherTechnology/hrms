<?php

include '../../ajaxconfig.php';

$staff_id = $_POST['staff_id'];

// Core query to fetch historical milestones from occupation_info
$query = "
SELECT
    oi.id,
    oi.staff_profile_id,
    oi.department,
    oi.branch_id,
    oi.total_ctc,

    CASE
        WHEN oi.occ_status = 0 THEN sc.joining_date
        ELSE oi.effective_from
    END AS event_date,
    oi.occ_status,
    sc.staff_name,
    sc.joining_date,
    sc.staff_id,
    sc.staff_type,
    d.department_name,
    b.branch_name,
    dc.designation,
    ti.team_name

FROM occupation_info oi

LEFT JOIN staff_creation sc ON sc.id = oi.staff_profile_id
LEFT JOIN department_creation d ON d.id = oi.department
LEFT JOIN branch_creation b ON b.id = oi.branch_id
LEFT JOIN designation_creation dc ON dc.id = oi.designation
LEFT JOIN team_name_creation ti ON oi.team = ti.id

WHERE oi.staff_profile_id = '$staff_id'

ORDER BY event_date ASC, oi.id ASC
";

$result = $pdo->query($query);
$data = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    switch ($row['occ_status']) {
        case 0: $row['event_name'] = 'Joined'; break;
        case 1: $row['event_name'] = 'Promotion'; break;
        case 2: $row['event_name'] = 'Transfer'; break;
        case 3: $row['event_name'] = 'Increment'; break;
    }

    $profile_id = $row['staff_profile_id'];
    $current_total_ctc = $row['total_ctc']; // Extract the CTC specifically tied to this milestone event
    
    // --- FIXED: Added total_ctc condition to match components to the correct timeline event ---
    $allowance_query = "
        SELECT 
            cc.salary_component,
            cc.pay_frequency,
            cc.component_classification,
            sci.ctc_amount,
            sci.total_amount
        FROM staff_ctc_info sci
        JOIN ctc_creation cc ON sci.ctc_id = cc.id
        WHERE sci.staff_profile_id = '$profile_id' 
          AND sci.total_ctc = '$current_total_ctc'
    ";
    
    $allowance_result = $pdo->query($allowance_query);
    
    $row['allowances'] = [];
    $row['total_amount'] = $row['total_ctc']; // Default fallback

    while ($allowance_row = $allowance_result->fetch(PDO::FETCH_ASSOC)) {
        // Set the total_amount dynamically from the matched table row 
        $row['total_amount'] = $allowance_row['total_amount'];

        // Only append to the allowances array if it is a Non-CTC component (classification = 2)
        if (($allowance_row['component_classification']) == 2) {
            $row['allowances'][] = [
                'salary_component' => $allowance_row['salary_component'],
                'pay_frequency' => $allowance_row['pay_frequency'],
                'ctc_amount' => $allowance_row['ctc_amount']
            ];
        }
    }
    // -----------------------------------------------------------------..,,,,,,,,,,
    $data[] = $row;
}

echo json_encode($data);