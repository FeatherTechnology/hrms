<?php

require '../../ajaxconfig.php';

$response = [];

try {

    $company_id = $_POST['company_id'] ?? '';
    $shift_id   = $_POST['shift_id'] ?? '';
    $staff_id   = $_POST['staff_id'] ?? '';
    $date       = $_POST['date'] ?? '';

    $where = [];
    $params = [];

    if (!empty($company_id)) {
        $where[] = "st.company_id = :company_id";
        $params[':company_id'] = $company_id;
    }

    if (!empty($shift_id)) {
        $where[] = "oi.shift = :shift_id";
        $params[':shift_id'] = $shift_id;
    }

    if (!empty($staff_id) && $staff_id != 'all') {
        $where[] = "a.staff_profile_id = :staff_id";
        $params[':staff_id'] = $staff_id;
    }

    if (!empty($date)) {
        $where[] = "DATE(a.entry_time) = :date";
        $params[':date'] = $date;
    }

    $where_sql = '';

    if (!empty($where)) {
        $where_sql = "WHERE " . implode(' AND ', $where);
    }

    $query = "SELECT
    a.staff_profile_id,
    st.staff_name,
    oi.shift,
    sc.shift_name,
    sc.start_time,
    sc.end_time,
    sc.grace_time,
    a.entry_time

    FROM attendance a

    LEFT JOIN staff_creation st ON st.id = a.staff_profile_id
    LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = a.staff_profile_id)
    LEFT JOIN shift_creation sc ON sc.id = oi.shift

    $where_sql

    ORDER BY a.entry_time ASC
    ";

    $stmt = $pdo->prepare($query);

    $stmt->execute($params);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {

        $entry_time = strtotime($row['entry_time']);

        // ================= SHIFT START / END =================

        $shift_start = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['start_time']
        );

        $shift_end = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['end_time']
        );

        // Night Shift Support
        if ($shift_end <= $shift_start) {
            $shift_end = strtotime('+1 day', $shift_end);
        }

        // ================= SHIFT MIDPOINT =================
        // This will automatically split any shift into First Half / Second Half.

        $shift_duration = $shift_end - $shift_start;
        $mid_shift = $shift_start + ($shift_duration / 2);

        // ================= GRACE TIME =================

        $grace_minutes = 0;

        if (!empty($row['grace_time'])) {

            preg_match('/\d+/', $row['grace_time'], $match);

            $grace_minutes = isset($match[0])
                ? (int)$match[0]
                : 0;
        }


        // Employee actually starts working
        $working_start = max($entry_time, $shift_start);

        // Arrays used later
        $permissions = [];
        $blockedTimes = [];

        // =========================================
        // GET REGULARIZATION (Permission / OT / Leave)
        // =========================================

        $regQry = $pdo->prepare("SELECT
        r.req_type,
        r.leave_type,
        r.leave_period,
        r.from_date,
        r.to_date,
        lc.leave_type AS leave_name
        FROM regularization r
        LEFT JOIN leave_creation lc
            ON lc.id = r.leave_type
        WHERE r.staff_profile_id = :staff_id
        AND r.status = 1
        AND DATE(r.from_date)=:att_date
        ORDER BY r.from_date ASC");

        $regQry->execute([
            ':staff_id' => $row['staff_profile_id'],
            ':att_date' => date('Y-m-d', $entry_time)
        ]);

        $regularizations = $regQry->fetchAll(PDO::FETCH_ASSOC);

        $leaveStart = null;
        $leaveEnd   = null;
        $leaveName  = '';

        foreach ($regularizations as $reg) {

            // =====================================
            // Permission
            // =====================================

            if (
                $reg['req_type'] == 2 &&
                !empty($reg['from_date']) &&
                !empty($reg['to_date'])
            ) {

                $permissionStart = strtotime($reg['from_date']);
                $permissionEnd   = strtotime($reg['to_date']);

                $permissions[] = [
                    'start' => $permissionStart,
                    'end'   => $permissionEnd
                ];

                $blockedTimes[] = [
                    'start' => $permissionStart,
                    'end'   => $permissionEnd
                ];

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Permission Hours',
                    'color'      => '#FF9800',
                    'start'      => $reg['from_date'],
                    'end'        => $reg['to_date']
                ];
            }

            // =====================================
            // Leave
            // =====================================

            if (in_array($reg['req_type'], [1, 3])) {

                if ($reg['req_type'] == 1) {

                    $leaveName = !empty($reg['leave_name'])
                        ? $reg['leave_name']
                        : 'Leave';
                } else {

                    $leaveName = 'Week Off';
                }

                switch ($reg['leave_period']) {

                    // First Half
                    case 1:

                        $leaveStart = $shift_start;
                        $leaveEnd   = $mid_shift;

                        break;

                    // Second Half
                    case 2:

                        $leaveStart = $mid_shift;
                        $leaveEnd   = $shift_end;

                        break;

                    // Full Day
                    case 3:

                        $leaveStart = $shift_start;
                        $leaveEnd   = $shift_end;

                        break;
                }

                if ($leaveStart && $leaveEnd) {

                    $blockedTimes[] = [
                        'start' => $leaveStart,
                        'end'   => $leaveEnd
                    ];

                    $response[] = [
                        'staff_name' => $row['staff_name'],
                        'type'       => $leaveName,
                        'color' => ($reg['req_type'] == 1)
                            ? '#009688'      // Leave
                            : '#9E9E9E',     // Week Off
                        'start'      => date('Y-m-d H:i:s', $leaveStart),
                        'end'        => date('Y-m-d H:i:s', $leaveEnd)
                    ];
                }
            }
        }

        // Sort blocked intervals
        usort($blockedTimes, function ($a, $b) {
            return $a['start'] <=> $b['start'];
        });

        // ================================== CALCULATE EFFECTIVE SHIFT START ========================================= //

        $effectiveShiftStart = $shift_start;

        // First Half Leave
        if (
            $leaveStart &&
            $leaveEnd &&
            $leaveStart == $shift_start
        ) {
            $effectiveShiftStart = $leaveEnd;
        }

        // Grace starts after effective shift start
        $grace_end = strtotime(
            '+' . $grace_minutes . ' minutes',
            $effectiveShiftStart
        );

        // ========================================= GRACE BAR ========================================= //

        if (
            $grace_minutes > 0 &&
            $entry_time > $effectiveShiftStart
        ) {

            $grace_bar_end = min(
                $entry_time,
                $grace_end
            );

            if ($grace_bar_end > $effectiveShiftStart) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Grace Time',
                    'color'      => '#9C27B0',
                    'start'      => date('Y-m-d H:i:s', $effectiveShiftStart),
                    'end'        => date('Y-m-d H:i:s', $grace_bar_end)
                ];
            }
        }

        // =========================================
        // LATE ENTRY
        // =========================================

        if ($entry_time > $grace_end) {

            $response[] = [
                'staff_name' => $row['staff_name'],
                'type'       => 'Late Entry',
                'color'      => '#F44336',
                'start'      => date('Y-m-d H:i:s', $grace_end),
                'end'        => date('Y-m-d H:i:s', $entry_time)
            ];
        }

        // ======================================== WORKING HOURS ========================================= //

        $currentStart = $working_start;

        // If employee is already after shift end, no working hours
        if ($currentStart < $shift_end) {

            // No Permission / No Leave
            if (empty($blockedTimes)) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Working Hours',
                    'color'      => '#4CAF50',
                    'start'      => date('Y-m-d H:i:s', $currentStart),
                    'end'        => date('Y-m-d H:i:s', $shift_end)
                ];
            } else {

                foreach ($blockedTimes as $block) {

                    // Ignore anything completely outside shift
                    if ($block['end'] <= $shift_start || $block['start'] >= $shift_end) {
                        continue;
                    }

                    // Restrict block within shift
                    $blockStart = max($block['start'], $shift_start);
                    $blockEnd   = min($block['end'], $shift_end);

                    // Working period before blocked interval
                    if ($currentStart < $blockStart) {

                        $response[] = [
                            'staff_name' => $row['staff_name'],
                            'type'       => 'Working Hours',
                            'color'      => '#4CAF50',
                            'start'      => date('Y-m-d H:i:s', $currentStart),
                            'end'        => date('Y-m-d H:i:s', $blockStart)
                        ];
                    }

                    // Move current pointer after blocked interval
                    if ($currentStart < $blockEnd) {
                        $currentStart = $blockEnd;
                    }
                }

                // Remaining working hours after last blocked interval
                if ($currentStart < $shift_end) {

                    $response[] = [
                        'staff_name' => $row['staff_name'],
                        'type'       => 'Working Hours',
                        'color'      => '#4CAF50',
                        'start'      => date('Y-m-d H:i:s', $currentStart),
                        'end'        => date('Y-m-d H:i:s', $shift_end)
                    ];
                }
            }
        }
    }

    // <------ OT ----->
    $otQry = $pdo->prepare("SELECT
        st.staff_name,
        r.from_date,
        r.to_date
    FROM regularization r
    LEFT JOIN staff_creation st
        ON st.id = r.staff_profile_id
    WHERE r.req_type = 4
    AND r.status = 1
    AND DATE(r.from_date) = :date
    ");

    $otQry->execute([
        ':date' => $date
    ]);

    $otResult = $otQry->fetchAll(PDO::FETCH_ASSOC);

    foreach ($otResult as $ot) {

        $response[] = [
            'staff_name' => $ot['staff_name'],
            'type'       => 'OT Hours',
            'color'      => '#2196F3',
            'start'      => $ot['from_date'],
            'end'        => $ot['to_date']
        ];
    }

    // First Half / Second Half / Full Day Leave and Week Off Requests without attendance
    $leaveQry = $pdo->prepare("SELECT
        r.req_type,
        r.staff_profile_id,
        st.staff_name,
        oi.shift,
        sc.shift_name,
        sc.start_time,
        sc.end_time,
        lc.leave_type AS leave_name,
        r.leave_period
    FROM regularization r
    LEFT JOIN staff_creation st
        ON st.id = r.staff_profile_id
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = r.staff_profile_id
        )
    LEFT JOIN shift_creation sc
        ON sc.id = oi.shift
    LEFT JOIN leave_creation lc
        ON lc.id = r.leave_type
    WHERE r.req_type IN (1,3)
    AND r.status = 1
    AND :date BETWEEN DATE(r.from_date) AND DATE(r.to_date)
    AND NOT EXISTS (
    SELECT 1
    FROM attendance a
    WHERE a.staff_profile_id = r.staff_profile_id
    AND DATE(a.entry_time)=:date)
    AND NOT EXISTS (
    SELECT 1
    FROM attendance a
    WHERE a.staff_profile_id = r.staff_profile_id
    AND DATE(a.entry_time)=:date)
    ");

    $leaveQry->execute([
        ':date' => $date
    ]);

    $leaveResult = $leaveQry->fetchAll(PDO::FETCH_ASSOC);

    foreach ($leaveResult as $leaveRow) {

        $shift_start = strtotime($date . ' ' . $leaveRow['start_time']);
        $shift_end   = strtotime($date . ' ' . $leaveRow['end_time']);

        if ($shift_end <= $shift_start) {
            $shift_end = strtotime('+1 day', $shift_end);
        }

        $mid_shift = $shift_start + (($shift_end - $shift_start) / 2);

        switch ($leaveRow['leave_period']) {

            case 1:

                $leaveStart = $shift_start;
                $leaveEnd   = $mid_shift;
                break;

            case 2:

                $leaveStart = $mid_shift;
                $leaveEnd   = $shift_end;
                break;

            case 3:

                $leaveStart = $shift_start;
                $leaveEnd   = $shift_end;
                break;
        }

        $response[] = [
            'staff_name' => $leaveRow['staff_name'],
            'type' => ($leaveRow['req_type'] == 1)
                ? $leaveRow['leave_name']
                : 'Week Off',
            'color' => ($leaveRow['req_type'] == 1)
                ? '#009688'
                : '#9E9E9E',
            'start' => date('Y-m-d H:i:s', $leaveStart),
            'end' => date('Y-m-d H:i:s', $leaveEnd)
        ];

        // Remaining shift becomes LOP if there is no attendance

        if ($leaveRow['leave_period'] == 1) {

            // First Half Leave -> Second Half LOP
            $response[] = [
                'staff_name' => $leaveRow['staff_name'],
                'type'       => 'LOP',
                'color'      => '#424242',
                'start'      => date('Y-m-d H:i:s', $mid_shift),
                'end'        => date('Y-m-d H:i:s', $shift_end)
            ];
        } elseif ($leaveRow['leave_period'] == 2) {

            // Second Half Leave -> First Half LOP
            $response[] = [
                'staff_name' => $leaveRow['staff_name'],
                'type'       => 'LOP',
                'color'      => '#424242',
                'start'      => date('Y-m-d H:i:s', $shift_start),
                'end'        => date('Y-m-d H:i:s', $mid_shift)
            ];
        }
    }

    $lopWhere = [];
    $lopParams = [
        ':date' => $date
    ];

    if (!empty($company_id)) {
        $lopWhere[] = "st.company_id = :company_id";
        $lopParams[':company_id'] = $company_id;
    }

    if (!empty($shift_id)) {
        $lopWhere[] = "oi.shift = :shift_id";
        $lopParams[':shift_id'] = $shift_id;
    }

    if (!empty($staff_id) && $staff_id != 'all') {
        $lopWhere[] = "st.id = :staff_id";
        $lopParams[':staff_id'] = $staff_id;
    }

    $lopWhereSql = '';

    if (!empty($lopWhere)) {
        $lopWhereSql = "AND " . implode(" AND ", $lopWhere);
    }

    $lopQry = $pdo->prepare("SELECT
        st.id AS staff_profile_id,
        st.staff_name,
        oi.shift,
        sc.start_time,
        sc.end_time
    FROM staff_creation st
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = st.id
        )
    LEFT JOIN shift_creation sc
        ON sc.id = oi.shift
    WHERE 1
    $lopWhereSql

    AND NOT EXISTS (
        SELECT 1
        FROM attendance a
        WHERE a.staff_profile_id = st.id
        AND DATE(a.entry_time) = :date
    )

    AND NOT EXISTS (
        SELECT 1
        FROM regularization r
        WHERE r.staff_profile_id = st.id
        AND r.req_type IN (1,3)
        AND r.status = 1
        AND :date BETWEEN DATE(r.from_date) AND DATE(r.to_date)
    )
    ");

    $lopQry->execute($lopParams);
    $lopResult = $lopQry->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lopResult as $lopRow) {

        $shift_start = strtotime($date . ' ' . $lopRow['start_time']);
        $shift_end   = strtotime($date . ' ' . $lopRow['end_time']);

        if ($shift_end <= $shift_start) {
            $shift_end = strtotime('+1 day', $shift_end);
        }

        $response[] = [
            'staff_name' => $lopRow['staff_name'],
            'type'       => 'LOP',
            'color'      => '#424242',
            'start'      => date('Y-m-d H:i:s', $shift_start),
            'end'        => date('Y-m-d H:i:s', $shift_end)
        ];
    }
} catch (PDOException $e) { // try ends here

    $response = [
        'status' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
