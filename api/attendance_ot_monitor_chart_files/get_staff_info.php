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

        $shift_start = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['start_time']
        );

        $shift_end = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['end_time']
        );

        // Night Shift
        if ($shift_end <= $shift_start) {
            $shift_end = strtotime('+1 day', $shift_end);
        }

        // Grace Minutes
        $grace_minutes = 0;

        if (!empty($row['grace_time'])) {

            preg_match('/\d+/', $row['grace_time'], $match);

            $grace_minutes = isset($match[0])
                ? (int)$match[0]
                : 0;
        }

        $grace_end = strtotime(
            '+' . $grace_minutes . ' minutes',
            $shift_start
        );

        // Working start
        $working_start = max($entry_time, $shift_start);

        // ========================================= GET REGULARIZATION ========================================= //

        $regQry = $pdo->prepare("
        SELECT
            req_type,
            from_date,
            to_date
        FROM regularization
        WHERE staff_profile_id = :staff_id
        AND status = 1
        AND req_type IN (2,4)
        AND DATE(from_date) = :att_date
        ORDER BY from_date ASC
    ");

        $regQry->execute([
            ':staff_id' => $row['staff_profile_id'],
            ':att_date' => date('Y-m-d', $entry_time)
        ]);

        $regularizations = $regQry->fetchAll(PDO::FETCH_ASSOC);

        $permissions = [];

        foreach ($regularizations as $reg) {

            // Permission
            if (
                $reg['req_type'] == 2 &&
                !empty($reg['from_date']) &&
                !empty($reg['to_date'])
            ) {

                $permissions[] = [
                    'start' => strtotime($reg['from_date']),
                    'end'   => strtotime($reg['to_date'])
                ];

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Permission Hours',
                    'color'      => '#FBBC05',
                    'start'      => $reg['from_date'],
                    'end'        => $reg['to_date']
                ];
            }

            // OT
            if (
                $reg['req_type'] == 4 &&
                !empty($reg['from_date']) &&
                !empty($reg['to_date'])
            ) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'OT Hours',
                    'color'      => '#4285F4',
                    'start'      => $reg['from_date'],
                    'end'        => $reg['to_date']
                ];
            }
        }

        // ========================================= GRACE BAR ========================================= //

        if (
            $grace_minutes > 0 &&
            $entry_time > $shift_start
        ) {

            $grace_bar_end = min(
                $entry_time,
                $grace_end
            );

            if ($grace_bar_end > $shift_start) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Grace Time',
                    'color'      => '#A142F4',
                    'start'      => date('Y-m-d H:i:s', $shift_start),
                    'end'        => date('Y-m-d H:i:s', $grace_bar_end)
                ];
            }
        }

        // ========================================= LATE ENTRY ========================================= //

        if ($entry_time > $grace_end) {

            $response[] = [
                'staff_name' => $row['staff_name'],
                'type'       => 'Late Entry',
                'color'      => '#EA4335',
                'start'      => date('Y-m-d H:i:s', $grace_end),
                'end'        => date('Y-m-d H:i:s', $entry_time)
            ];
        }

        // ======================================== WORKING HOURS ========================================= //

        if (empty($permissions)) {

            if ($working_start < $shift_end) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Working Hours',
                    'color'      => '#66AA00',
                    'start'      => date('Y-m-d H:i:s', $working_start),
                    'end'        => date('Y-m-d H:i:s', $shift_end)
                ];
            }
        } else {

            $currentStart = $working_start;

            foreach ($permissions as $permission) {

                if ($currentStart < $permission['start']) {

                    $response[] = [
                        'staff_name' => $row['staff_name'],
                        'type'       => 'Working Hours',
                        'color'      => '#66AA00',
                        'start'      => date('Y-m-d H:i:s', $currentStart),
                        'end'        => date('Y-m-d H:i:s', $permission['start'])
                    ];
                }

                $currentStart = min(
                    $shift_end,
                    max($currentStart, $permission['end'])
                );
            }

            if ($currentStart < $shift_end) {

                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Working Hours',
                    'color'      => '#66AA00',
                    'start'      => date('Y-m-d H:i:s', $currentStart),
                    'end'        => date('Y-m-d H:i:s', $shift_end)
                ];
            }
        }
    } // foreach ends here
} catch (PDOException $e) { // try ends here

    $response = [
        'status' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
