<?php
// Get leave, permission, week-off, or OT balance for selected staff.

require "../../ajaxconfig.php";

$req_type     = $_POST['req_type'] ?? '';
$cmpy_id      = $_POST['cmpy_id'] ?? '';
$ot_pay      = $_POST['ot_pay'] ?? '';
$staff_id     = $_POST['staff_id'] ?? '';
$from_Date    = $_POST['from_date'] ?? '';
$to_date      = $_POST['to_date'] ?? '';
$leave_period = $_POST['leave_period'] ?? '';
$leave_type   = $_POST['leave_type'] ?? '';
$dep_id   = $_POST['dep_id'] ?? '';

$query = "";


/* ================= LEAVE ================= */

if ($req_type == '1') {

    /* ================= LOP ================= */

    if ($leave_type == '0') {

        $query = "SELECT
            sc.start_time,
            sc.end_time,
            (
                SELECT COALESCE(SUM(
                    CASE
                        WHEN reg.leave_period IN ('1', '2') THEN 0.5
                        WHEN reg.leave_period = '3' THEN 1
                        ELSE 0
                    END
                ), 0)
                FROM regularization reg
                WHERE reg.staff_profile_id = :staff_id
                AND reg.company_id = :cmpy_id
                AND reg.req_type = 1
                AND reg.leave_type = 0
                AND YEAR(reg.from_date) = YEAR(CURDATE())
                AND reg.status IN (0, 1)
            ) AS cur_month_lop_count
        FROM occupation_info oi
        INNER JOIN shift_creation sc
            ON sc.id = oi.shift
        WHERE oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = :staff_id
        )";
    } else {

        /* ================= NORMAL LEAVE ================= */

        $query = "SELECT 
            lc.no_of_days,
            sc.start_time,
            sc.end_time,

            COALESCE(
                SUM(
                    CASE
                        WHEN reg.leave_period IN (1,2) THEN 0.5
                        ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                    END
                ), 0
            ) AS already_used,

            (
                lc.no_of_days - COALESCE(
                    SUM(
                        CASE
                            WHEN reg.leave_period IN (1,2) THEN 0.5
                            ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                        END
                    ), 0
                )
            ) AS ave_balance,

            lc.no_of_days -
            (
                COALESCE(
                    SUM(
                        CASE
                            WHEN reg.leave_period IN (1,2) THEN 0.5
                            ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                        END
                    ), 0
                )
                +
                CASE
                    WHEN :leave_period IN (1,2) THEN 0.5
                    ELSE DATEDIFF(:to_date,:from_date) + 1
                END
            ) AS balance

        FROM leave_creation lc

        LEFT JOIN regularization reg
            ON reg.company_id = lc.company_id
            AND reg.staff_profile_id = :staff_id
            AND reg.leave_type = lc.id
            AND YEAR(reg.from_date) = YEAR(CURDATE())
            AND reg.status IN (0,1)

        LEFT JOIN occupation_info oi
            ON oi.id = (
                SELECT MAX(id)
                FROM occupation_info
                WHERE staff_profile_id = :staff_id
            )

        INNER JOIN shift_creation sc 
            ON sc.id = oi.shift

        WHERE lc.company_id = :cmpy_id
        AND lc.id = :leave_type";
    }
}


/* ================= PERMISSION ================= */ else if ($req_type == '2') {

    $query = "SELECT
        sc.start_time,
        sc.end_time,
        sc.grace_time,

        cp.permission_type,
        cp.max_permission,

        COUNT(reg.id) AS used_count,

        CASE

            WHEN cp.permission_type = 1 THEN

                GREATEST(
                    cp.max_permission - COUNT(reg.id),
                    0
                )

            WHEN cp.permission_type = 2 THEN

                GREATEST(
                    cp.max_permission -
                    COALESCE(
                        SUM(
                            CASE
                                WHEN reg.from_date IS NOT NULL
                                AND reg.to_date IS NOT NULL
                                THEN TIMESTAMPDIFF(
                                    MINUTE,
                                    reg.from_date,
                                    reg.to_date
                                )
                                ELSE 0
                            END
                        ),
                        0
                    ),
                    0
                )

            ELSE 0

        END AS ave_balance";


    /* ================= DATE SELECTED ================= */

    if (!empty($from_Date) && !empty($to_date)) {

        $query .= ",

        CASE

            WHEN cp.permission_type = 1 THEN

                GREATEST(
                    cp.max_permission
                    - COUNT(reg.id)
                    - 1,
                    0
                )

            WHEN cp.permission_type = 2 THEN

                GREATEST(
                    cp.max_permission
                    -
                    COALESCE(
                        SUM(
                            CASE
                                WHEN reg.from_date IS NOT NULL
                                AND reg.to_date IS NOT NULL
                                THEN TIMESTAMPDIFF(
                                    MINUTE,
                                    reg.from_date,
                                    reg.to_date
                                )
                                ELSE 0
                            END
                        ),
                        0
                    )
                    -
                    TIMESTAMPDIFF(
                        MINUTE,
                        :permission_from_date,
                        :permission_to_date
                    ),
                    0
                )

            ELSE 0

        END AS balance,

        CASE

            WHEN cp.permission_type = 1 THEN

                CASE
                    WHEN COUNT(reg.id) + 1 > cp.max_permission
                    THEN 1
                    ELSE 0
                END

            WHEN cp.permission_type = 2 THEN

                CASE
                    WHEN
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN reg.from_date IS NOT NULL
                                    AND reg.to_date IS NOT NULL
                                    THEN TIMESTAMPDIFF(
                                        MINUTE,
                                        reg.from_date,
                                        reg.to_date
                                    )
                                    ELSE 0
                                END
                            ),
                            0
                        )
                        +
                        TIMESTAMPDIFF(
                            MINUTE,
                            :permission_from_date_2,
                            :permission_to_date_2
                        )
                        > cp.max_permission
                    THEN 1
                    ELSE 0
                END

            ELSE 0

        END AS permission_exceeded";
    }


    $query .= "

    FROM company_policies cp
INNER JOIN (
    SELECT company_id, department_id, MAX(effective_date) AS effective_date
    FROM company_policies
    WHERE effective_date <= CURDATE()
    GROUP BY company_id, department_id
) latest_policy
    ON latest_policy.company_id = cp.company_id
    AND latest_policy.department_id = cp.department_id
    AND latest_policy.effective_date = cp.effective_date
LEFT JOIN regularization reg
    ON reg.company_id = cp.company_id
    AND reg.req_type = :req_type
    AND reg.staff_profile_id = :staff_id
    AND YEAR(reg.from_date) = YEAR(CURDATE())
    AND MONTH(reg.from_date) = MONTH(CURDATE())
    AND reg.status IN (0, 1)
    AND reg.permission_type = cp.permission_type
LEFT JOIN occupation_info oi
    ON oi.id = (
        SELECT MAX(id)
        FROM occupation_info
        WHERE staff_profile_id = :permission_staff_id
    )
INNER JOIN shift_creation sc ON sc.id = oi.shift
WHERE cp.company_id = :cmpy_id
    AND cp.department_id = :dep_id
GROUP BY
    cp.id,
    cp.permission_type,
    cp.max_permission,
    sc.start_time,
    sc.end_time,
    sc.grace_time";
}


/* ================= WEEK OFF ================= */ else if ($req_type == '3') {

    $query = "SELECT
        COALESCE((
            SELECT SUM(cw.week_off)
            FROM company_weekoffs cw
            WHERE cw.company_id = :weekoff_company_id
        ), 0) AS no_of_days,
        sc.start_time,
        sc.end_time,
        COALESCE((
            SELECT SUM(
                CASE
                    WHEN reg.leave_period IN (1,2) THEN 0.5
                    ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                END
            )
            FROM regularization reg
            WHERE reg.company_id = :used_company_id
                AND reg.req_type = 3
                AND reg.staff_profile_id = :used_staff_id
                AND MONTH(reg.from_date) = MONTH(CURDATE())
                AND YEAR(reg.from_date) = YEAR(CURDATE())
                AND reg.status IN (0,1)
        ), 0) AS used_count,
        GREATEST(
            COALESCE((
                SELECT SUM(cw.week_off)
                FROM company_weekoffs cw
                WHERE cw.company_id = :balance_company_id
            ), 0)
            -
            COALESCE((
                SELECT SUM(
                    CASE
                        WHEN reg.leave_period IN (1,2) THEN 0.5
                        ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                    END
                )
                FROM regularization reg
                WHERE reg.company_id = :balance_used_company_id
                    AND reg.req_type = 3
                    AND reg.staff_profile_id = :balance_staff_id
                    AND MONTH(reg.from_date) = MONTH(CURDATE())
                    AND YEAR(reg.from_date) = YEAR(CURDATE())
                    AND reg.status IN (0,1)
            ), 0),
            0
        ) AS ave_balance
    FROM occupation_info oi
    INNER JOIN shift_creation sc ON sc.id = oi.shift
    WHERE oi.id = (
        SELECT MAX(id)
        FROM occupation_info
        WHERE staff_profile_id = :shift_staff_id
    )";

    if (!empty($from_Date) && !empty($to_date)) {
        $query = str_replace(
            'GREATEST(
            COALESCE((',
            'GREATEST(
            COALESCE((',
            $query
        );
        $query = str_replace(
            '), 0
        ) AS ave_balance',
            '), 0
        ) AS ave_balance',
            $query
        );
    }
}


/* ================= OT ================= */ else if ($req_type == '4') {
    $ot_pay = $_POST['ot_pay'] ?? 0;

    $query = "SELECT
        sc.start_time,
        sc.end_time,
        (
            SELECT
                CASE
    WHEN :ot_payment = 1 THEN
        COALESCE(SUM(total_min), 0)
    ELSE
        COUNT(id)
END
            FROM regularization
            WHERE req_type = :req_type
              AND staff_profile_id = :ot_staff_id
              AND company_id = :cmpy_id
              AND status IN (0,1)
              AND MONTH(from_date) = MONTH(CURDATE())
              AND YEAR(from_date) = YEAR(CURDATE())
        ) AS current_month_ot_count
    FROM occupation_info oi
    INNER JOIN shift_creation sc ON sc.id = oi.shift
    WHERE oi.id = (
        SELECT MAX(id)
        FROM occupation_info
        WHERE staff_profile_id = :shift_staff_id
    )";
}


/* ================= PREPARE ================= */

$stmt = $pdo->prepare($query);


/* ================= BIND ================= */

if ($req_type == '1') {

    if ($leave_type == '0') {

        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':cmpy_id', $cmpy_id);
    } else {

        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':cmpy_id', $cmpy_id);
        $stmt->bindParam(':from_date', $from_Date);
        $stmt->bindParam(':to_date', $to_date);
        $stmt->bindParam(':leave_type', $leave_type);
        $stmt->bindParam(':leave_period', $leave_period, PDO::PARAM_INT);
    }
} else if ($req_type == '2') {

    $stmt->bindParam(':req_type', $req_type, PDO::PARAM_INT);
    $stmt->bindParam(':cmpy_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindParam(':dep_id', $dep_id);
    $stmt->bindParam(':staff_id', $staff_id);

    /*
        Separate parameter for occupation_info
    */
    $stmt->bindParam(':permission_staff_id', $staff_id);

    if (!empty($from_Date) && !empty($to_date)) {

        $stmt->bindParam(
            ':permission_from_date',
            $from_Date
        );

        $stmt->bindParam(
            ':permission_to_date',
            $to_date
        );

        $stmt->bindParam(
            ':permission_from_date_2',
            $from_Date
        );

        $stmt->bindParam(
            ':permission_to_date_2',
            $to_date
        );
    }
} else if ($req_type == '3') {

    $stmt->bindValue(':weekoff_company_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindValue(':used_company_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindValue(':used_staff_id', $staff_id, PDO::PARAM_INT);
    $stmt->bindValue(':balance_company_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindValue(':balance_used_company_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindValue(':balance_staff_id', $staff_id, PDO::PARAM_INT);
    $stmt->bindValue(':shift_staff_id', $staff_id, PDO::PARAM_INT);
} else if ($req_type == '4') {
    $stmt->bindValue(':ot_payment', $ot_pay, PDO::PARAM_INT);
    $stmt->bindValue(':req_type', $req_type, PDO::PARAM_INT);
    $stmt->bindValue(':ot_staff_id', $staff_id);
    $stmt->bindValue(':cmpy_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindValue(':shift_staff_id', $staff_id);
}

/* ================= EXECUTE ================= */

$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);


/* ================= WEEK OFF SUSPENSION DEDUCTION ================= */

if ($req_type == '3' && $result) {

    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    $totalWeekoff = (float)($result['no_of_days'] ?? 0);
    $usedCount = (float)($result['used_count'] ?? 0);

    // Read configured weekday entitlements for this company.
    $weekoffStmt = $pdo->prepare("
        SELECT week_day, week_off
        FROM company_weekoffs
        WHERE company_id = :company_id
    ");
    $weekoffStmt->execute([':company_id' => $cmpy_id]);
    $weekoffRows = $weekoffStmt->fetchAll(PDO::FETCH_ASSOC);

    // Weekday values use MySQL DAYOFWEEK numbering: Sunday=1 ... Saturday=7.
    $weekdayMap = [
        'sunday' => 1, 'sun' => 1,
        'monday' => 2, 'mon' => 2,
        'tuesday' => 3, 'tue' => 3, 'tues' => 3,
        'wednesday' => 4, 'wed' => 4,
        'thursday' => 5, 'thu' => 5, 'thur' => 5,
        'friday' => 6, 'fri' => 6,
        'saturday' => 7, 'sat' => 7
    ];

    $configuredWeekoffs = [];

    foreach ($weekoffRows as $weekoff) {
        $day = strtolower(trim((string)($weekoff['week_day'] ?? '')));

        if (is_numeric($day)) {
            $dayNumber = (int)$day;
        } else {
            $dayNumber = $weekdayMap[$day] ?? 0;
        }

        if ($dayNumber >= 1 && $dayNumber <= 7) {
            $configuredWeekoffs[$dayNumber] = (int)$weekoff['week_off'];
        }
    }

    // Find suspensions that overlap the current month.
    $suspensionStmt = $pdo->prepare("
        SELECT
            GREATEST(DATE(from_date), :month_start) AS suspension_from,
            LEAST(DATE(to_date), :month_end) AS suspension_to
        FROM staff_suspended
        WHERE staff_profile_id = :staff_id
            AND DATE(from_date) <= :month_end_check
            AND DATE(to_date) >= :month_start_check
    ");

    $suspensionStmt->execute([
        ':month_start' => $monthStart,
        ':month_end' => $monthEnd,
        ':staff_id' => $staff_id,
        ':month_end_check' => $monthEnd,
        ':month_start_check' => $monthStart
    ]);

    $suspensions = $suspensionStmt->fetchAll(PDO::FETCH_ASSOC);

    // Keep each suspended calendar date once, even if suspension periods overlap.
    $suspendedDates = [];

    foreach ($suspensions as $suspension) {
        if (empty($suspension['suspension_from']) || empty($suspension['suspension_to'])) {
            continue;
        }

        $startDate = new DateTimeImmutable($suspension['suspension_from']);
        $endDate = new DateTimeImmutable($suspension['suspension_to']);

        for ($date = $startDate; $date <= $endDate; $date = $date->modify('+1 day')) {
            $suspendedDates[$date->format('Y-m-d')] = $date;
        }
    }

    // Count suspension dates that fall on configured weekoff weekdays.
    $suspendedWeekoffCounts = [];

    foreach ($suspendedDates as $date) {
        // PHP format 'w': Sunday=0 ... Saturday=6; convert to Sunday=1 ... Saturday=7.
        $dayNumber = (int)$date->format('w') + 1;

        if (isset($configuredWeekoffs[$dayNumber])) {
            $suspendedWeekoffCounts[$dayNumber] =
                ($suspendedWeekoffCounts[$dayNumber] ?? 0) + 1;
        }
    }

    // Do not deduct more than the configured monthly entitlement for each weekday.
    $suspensionDeduction = 0;

    foreach ($suspendedWeekoffCounts as $dayNumber => $count) {
        $suspensionDeduction += min(
            $count,
            $configuredWeekoffs[$dayNumber] ?? 0
        );
    }

    $availableWeekoffs = max(0, $totalWeekoff - $suspensionDeduction);

    $result['suspension_deduction'] = $suspensionDeduction;
    $result['available_days'] = $availableWeekoffs;
    $result['ave_balance'] = max(0, $availableWeekoffs - $usedCount);

    // Calculate remaining balance after including the selected request.
    if (!empty($from_Date) && !empty($to_date)) {
        if (in_array((int)$leave_period, [1, 2], true)) {
            $requestedDays = 0.5;
        } else {
            $requestStart = new DateTimeImmutable($from_Date);
            $requestEnd = new DateTimeImmutable($to_date);
            $requestedDays = $requestStart->diff($requestEnd)->days + 1;
        }

        $result['balance'] = max(
            0,
            $availableWeekoffs - $usedCount - $requestedDays
        );
    }
}



/* =========================================================
   PERMISSION ATTENDANCE CHECK
   ========================================================= */

if (
    $req_type == '2'
    && !empty($from_Date)
    && !empty($to_date)
    && $result
) {

    /*
        Find attendance for the selected FROM DATE.

        Example:

        from_date = 2026-10-06

        attendance.updated_time =
        2026-10-06 10:00:00
    */

    $attendanceQuery = "SELECT
        COALESCE(updated_time, entry_time) AS attendance_time

    FROM attendance

    WHERE staff_profile_id = :attendance_staff_id

    AND company_id = :attendance_company_id

    AND DATE(
        COALESCE(updated_time, entry_time)
    ) = DATE(:attendance_date)

    ORDER BY
        COALESCE(updated_time, entry_time) DESC

    LIMIT 1";


    $attendanceStmt = $pdo->prepare(
        $attendanceQuery
    );


    $attendanceStmt->execute([
        ':attendance_staff_id'   => $staff_id,
        ':attendance_company_id' => $cmpy_id,
        ':attendance_date'       => $from_Date
    ]);


    $attendanceResult =
        $attendanceStmt->fetch(PDO::FETCH_ASSOC);


    /*
        Default response values
    */

    $result['attendance_time'] = null;
    $result['permission_from'] = null;
    $result['permission_to'] = null;
    $result['late_entry'] = 0;


    /*
        Attendance found
    */

    if (
        $attendanceResult
        && !empty($attendanceResult['attendance_time'])
    ) {

        $attendanceTime =
            $attendanceResult['attendance_time'];


        /*
            Convert attendance datetime
            to only time.

            Example:
            2026-10-06 10:00:00

            becomes:
            10:00:00
        */

        $attendanceTimestamp =
            strtotime($attendanceTime);

        $attendanceOnlyTime =
            date(
                'H:i:s',
                $attendanceTimestamp
            );


        /*
            Shift start
        */

        $shiftStart =
            $result['start_time'];


        /*
            Grace time

            Example:
            15 = 15 minutes
        */

        $graceTime =
            $result['grace_time'] ?? 0;


        if (is_numeric($graceTime)) {

            $graceMinutes =
                (int)$graceTime;
        } else {

            /*
                If grace_time is stored as
                HH:MM:SS
            */

            $graceParts =
                explode(':', $graceTime);

            $graceMinutes = 0;

            if (count($graceParts) >= 2) {

                $graceMinutes =
                    ((int)$graceParts[0] * 60)
                    + (int)$graceParts[1];
            }
        }


        /*
            Shift start timestamp
        */

        $shiftStartTimestamp =
            strtotime($shiftStart);


        /*
            Shift start + grace
        */

        $graceEndTimestamp =
            strtotime(
                '+' . $graceMinutes . ' minutes',
                $shiftStartTimestamp
            );


        /*
            Attendance timestamp
        */

        $attendanceTimestampOnly =
            strtotime($attendanceOnlyTime);


        /*
            Check late entry
        */

        if (
            $attendanceTimestampOnly
            > $graceEndTimestamp
        ) {

            /*
                Late entry
            */

            $result['late_entry'] = 1;


            /*
                Permission starts from
                shift start.
            */

            $result['permission_from'] =
                date(
                    'H:i:s',
                    $shiftStartTimestamp
                );


            /*
                Permission ends at
                actual attendance time.
            */

            $result['permission_to'] =
                $attendanceOnlyTime;
        } else {

            /*
                Within grace period.
            */

            $result['late_entry'] = 0;

            $result['permission_from'] = null;

            $result['permission_to'] = null;
        }


        /*
            Actual attendance time
        */

        $result['attendance_time'] =
            $attendanceOnlyTime;
    }
}


/* ================= PERMISSION DISPLAY ================= */

if ($req_type == '2' && $result) {

    if ($result['permission_type'] == 2) {

        $result['ave_balance'] =
            $result['ave_balance'] . ' min';

        if (isset($result['balance'])) {

            $result['balance'] =
                $result['balance'] . ' min';
        }
    } else {

        $result['ave_balance'] =
            $result['ave_balance'] . '';

        if (isset($result['balance'])) {

            $result['balance'] =
                $result['balance'] . '';
        }
    }
}


/* ================= JSON RESPONSE ================= */

echo json_encode($result);
