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
    a.entry_time,
    r.req_type,
    r.approved_from_date,
    r.approved_to_date,
    r.approved_total_min

    FROM attendance a

    LEFT JOIN staff_creation st ON st.id = a.staff_profile_id
    LEFT JOIN occupation_info oi ON oi.id = (SELECT MAX(id) FROM occupation_info WHERE staff_profile_id = a.staff_profile_id)
    LEFT JOIN shift_creation sc ON sc.id = oi.shift
    LEFT JOIN regularization r ON r.staff_profile_id = a.staff_profile_id AND r.status = 1
    AND r.req_type IN (2,4) AND DATE(r.approved_from_date) = DATE(a.entry_time)

    $where_sql

    ORDER BY a.entry_time ASC
    ";

    $stmt = $pdo->prepare($query);

    $stmt->execute($params);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {

        // =========================================
        // BASIC TIMES
        // =========================================

        $entry_time = strtotime($row['entry_time']);

        $shift_start = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['start_time']
        );

        $shift_end = strtotime(
            date('Y-m-d', $entry_time) . ' ' . $row['end_time']
        );

        // =========================================
        // NIGHT SHIFT HANDLE
        // =========================================

        if ($shift_end <= $shift_start) {

            $shift_end = strtotime('+1 day', $shift_end);
        }

        // =========================================
        // GET GRACE MINUTES
        // =========================================

        $grace_minutes = 0;

        if (!empty($row['grace_time'])) {

            preg_match('/\d+/', $row['grace_time'], $match);

            $grace_minutes = isset($match[0])
                ? (int)$match[0]
                : 0;
        }

        // =========================================
        // OT BAR
        // =========================================

        if (
            $row['req_type'] == 4 &&
            !empty($row['approved_from_date']) &&
            !empty($row['approved_to_date'])
        ) {

            $response[] = [

                'staff_name' => $row['staff_name'],

                'type'       => 'OT Hours',

                'color'      => '#4285F4',

                'start'      => $row['approved_from_date'],

                'end'        => $row['approved_to_date']
            ];
        }

        // =========================================
        // PERMISSION MINUTES
        // =========================================

        $permission_minutes = 0;

        if (
            $row['req_type'] == 2 &&
            !empty($row['approved_total_min'])
        ) {

            $permission_minutes =
                (int)$row['approved_total_min'];
        }

        // =========================================
        // PERMISSION END
        // =========================================

        $permission_end = strtotime(
            '+' . $permission_minutes . ' minutes',
            $shift_start
        );

        // =========================================
        // GRACE END
        // =========================================

        $grace_end = strtotime(
            '+' . $grace_minutes . ' minutes',
            $permission_end
        );

        // =========================================
        // PERMISSION BAR
        // =========================================

        if ($permission_minutes > 0) {

            $response[] = [

                'staff_name' => $row['staff_name'],

                'type'       => 'Permission Hours',

                'color'      => '#FBBC05',

                'start'      => date(
                    'Y-m-d H:i:s',
                    $shift_start
                ),

                'end'        => date(
                    'Y-m-d H:i:s',
                    $permission_end
                )
            ];
        }

        // =========================================
        // GRACE BAR
        // =========================================

        if (
            $grace_minutes > 0 &&
            $entry_time > $permission_end
        ) {

            $grace_bar_end = min(
                $entry_time,
                $grace_end
            );

            $response[] = [

                'staff_name' => $row['staff_name'],

                'type'       => 'Grace Time',

                'color'      => '#A142F4',

                'start'      => date(
                    'Y-m-d H:i:s',
                    $permission_end
                ),

                'end'        => date(
                    'Y-m-d H:i:s',
                    $grace_bar_end
                )
            ];
        }

        // =========================================
        // LATE BAR
        // =========================================

        if ($entry_time > $grace_end) {

            $response[] = [

                'staff_name' => $row['staff_name'],

                'type'       => 'Later Entry',

                'color'      => '#EA4335',

                'start'      => date(
                    'Y-m-d H:i:s',
                    $grace_end
                ),

                'end'        => date(
                    'Y-m-d H:i:s',
                    $entry_time
                )
            ];
        }

        // =========================================
        // WORKING START
        // =========================================

        $working_start = max(
            $entry_time,
            $shift_start
        );

        // =========================================
        // WORKING BAR
        // =========================================

        if ($working_start < $shift_end) {

            $response[] = [

                'staff_name' => $row['staff_name'],

                'type'       => 'Working Hours',

                'color'      => '#66AA00',

                'start'      => date(
                    'Y-m-d H:i:s',
                    $working_start
                ),

                'end'        => date(
                    'Y-m-d H:i:s',
                    $shift_end
                )
            ];
        }
    }
} catch (PDOException $e) {

    $response = [
        'status' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
