<?php
// Get leave, permission, week-off, or OT balance for selected staff.

require "../../ajaxconfig.php";

$req_type     = $_POST['req_type'] ?? '';
$cmpy_id      = $_POST['cmpy_id'] ?? '';
$staff_id     = $_POST['staff_id'] ?? '';
$from_Date    = $_POST['from_date'] ?? '';
$to_date      = $_POST['to_date'] ?? '';
$leave_period = $_POST['leave_period'] ?? '';
$leave_type   = $_POST['leave_type'] ?? '';

$query = "";


/* ================= LEAVE ================= */

if ($req_type == '1') {

    /* ================= LOP ================= */

    if ($leave_type == '0') {

        $query = "SELECT
            sc.start_time,
            sc.end_time,

            (
                SELECT COUNT(reg.id)
                FROM regularization reg
                WHERE reg.staff_profile_id = :staff_id
                AND reg.company_id = :cmpy_id
                AND reg.req_type = 1
                AND reg.leave_type = 0
                AND YEAR(reg.from_date) = YEAR(CURDATE())
                AND reg.status IN (0,1)
            ) AS lop_count

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


/* ================= PERMISSION ================= */

else if ($req_type == '2') {

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

    LEFT JOIN regularization reg
        ON reg.company_id = cp.company_id
        AND reg.req_type = :req_type
        AND reg.staff_profile_id = :staff_id

        AND YEAR(reg.from_date) = YEAR(CURDATE())
        AND MONTH(reg.from_date) = MONTH(CURDATE())

        AND reg.status IN (0,1)

        AND reg.permission_type = cp.permission_type

    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = :permission_staff_id
        )

    INNER JOIN shift_creation sc
        ON sc.id = oi.shift

    WHERE cp.company_id = :cmpy_id

    GROUP BY
        cp.permission_type,
        cp.max_permission,
        sc.start_time,
        sc.end_time,
        sc.grace_time";
}


/* ================= WEEK OFF ================= */

else if ($req_type == '3') {

    $query = "SELECT 
        SUM(cw.week_off) AS no_of_days,
        sc.start_time,
        sc.end_time,

        COALESCE(
            SUM(
                CASE
                    WHEN reg.leave_period IN (1,2) THEN 0.5
                    ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                END
            ), 0
        ) AS used_count,

        (
            SUM(cw.week_off)
            -
            COALESCE(
                SUM(
                    CASE
                        WHEN reg.leave_period IN (1,2) THEN 0.5
                        ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                    END
                ), 0
            )
        ) AS ave_balance";

    if (!empty($from_Date) && !empty($to_date)) {

        $query .= ",
        (
            SUM(cw.week_off)
            -
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
                    ELSE DATEDIFF(:to_date, :from_date) + 1
                END
            )
        ) AS balance";
    }

    $query .= "
    FROM company_weekoffs cw

    LEFT JOIN company_policies cp 
        ON cp.id = cw.company_policies_id

    LEFT JOIN regularization reg
        ON reg.company_id = cp.company_id
        AND reg.req_type = :req_type
        AND reg.staff_profile_id = :staff_id
        AND MONTH(reg.from_date) = MONTH(CURDATE())
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

    WHERE cp.company_id = :cmpy_id";
}


/* ================= OT ================= */

else if ($req_type == '4') {

    $query = "SELECT
        sc.start_time,
        sc.end_time,

        (
            SELECT COUNT(id)
            FROM regularization
            WHERE req_type = :req_type
            AND staff_profile_id = :staff_id
            AND company_id = :cmpy_id
            AND status = 1
            AND MONTH(from_date) = MONTH(CURDATE())
            AND YEAR(from_date) = YEAR(CURDATE())
        ) AS current_month_ot_count

    FROM occupation_info oi

    INNER JOIN shift_creation sc
        ON sc.id = oi.shift

    WHERE oi.id = (
        SELECT MAX(id)
        FROM occupation_info
        WHERE staff_profile_id = :staff_id
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

    $stmt->bindParam(':req_type', $req_type, PDO::PARAM_INT);
    $stmt->bindParam(':cmpy_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindParam(':staff_id', $staff_id);

    if (!empty($from_Date) && !empty($to_date)) {

        $stmt->bindParam(':from_date', $from_Date);
        $stmt->bindParam(':to_date', $to_date);
        $stmt->bindParam(
            ':leave_period',
            $leave_period,
            PDO::PARAM_INT
        );
    }


} else if ($req_type == '4') {

    $stmt->bindParam(':req_type', $req_type, PDO::PARAM_INT);
    $stmt->bindParam(':cmpy_id', $cmpy_id, PDO::PARAM_INT);
    $stmt->bindParam(':staff_id', $staff_id);
}


/* ================= EXECUTE ================= */

$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);


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
?>