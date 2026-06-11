<?php

include '../../ajaxconfig.php';

$staff_id = $_POST['staff_id'];

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

-- FIXED: Sort explicitly by Date, then Status Hierarchy (0->1->2->3), then primary Key ID
ORDER BY event_date ASC, oi.occ_status ASC, oi.id ASC
";

$result = $pdo->query($query);
$data = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $row['staff_type'] = ($row['staff_type'] == 1) ? 'Employer' : 'Employee';
    switch ($row['occ_status']) {
        case 0: $row['event_name'] = 'Joined'; break;
        case 1: $row['event_name'] = 'Promotion'; break;
        case 2: $row['event_name'] = 'Transfer'; break;
        case 3: $row['event_name'] = 'Increment'; break;
    }
    $data[] = $row;
}

echo json_encode($data);