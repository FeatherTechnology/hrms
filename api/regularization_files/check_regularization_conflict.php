<?php
require_once '../../ajaxconfig.php';

try {
    $staff_profile_id = (int)($_POST['stf_prf_id'] ?? 0);
    $req_type = (int)($_POST['req_type'] ?? 0);
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';
    $shift_start = $_POST['shift_start'] ?? '';
    $shift_end = $_POST['shift_end'] ?? '';
    $leave_period = (int)($_POST['leave_period'] ?? 0);
    $hidden_id = (int)($_POST['hidden_id'] ?? 0);

    if (
        !$staff_profile_id ||
        !in_array($req_type, [1, 2, 3, 4], true) ||
        !$from_date ||
        !$to_date
    ) {
        echo json_encode([
            'result' => '2',
            'message' => 'Invalid request details.'
        ]);
        exit;
    }

    // OT does not require leave/permission/week-off conflict validation.
    if ($req_type === 4) {
        echo json_encode(['result' => '1']);
        exit;
    }

    $newStart = new DateTime($from_date);
    $newEnd = new DateTime($to_date);

    if ($newEnd < $newStart) {
        echo json_encode([
            'result' => '2',
            'message' => 'Invalid date range.'
        ]);
        exit;
    }

    // Leave and Week Off require a valid half-day/full-day selection.
    if (
        in_array($req_type, [1, 3], true) &&
        !in_array($leave_period, [1, 2, 3], true)
    ) {
        echo json_encode([
            'result' => '2',
            'message' => 'Please select a valid leave/week-off period.'
        ]);
        exit;
    }

    if (!$shift_start || !$shift_end) {
        echo json_encode([
            'result' => '2',
            'message' => 'Shift start and end times are required.'
        ]);
        exit;
    }

    $timeToMinutes = function ($value) {
        $time = new DateTime($value);
        return ((int)$time->format('H') * 60)
            + (int)$time->format('i');
    };

    $shiftStartMinutes = $timeToMinutes($shift_start);
    $shiftEndMinutes = $timeToMinutes($shift_end);

    if ($shiftEndMinutes <= $shiftStartMinutes) {
        echo json_encode([
            'result' => '2',
            'message' => 'Invalid shift time.'
        ]);
        exit;
    }

    $midMinutes = (int)(($shiftStartMinutes + $shiftEndMinutes) / 2);

    $getPeriod = function ($period) use (
        $shiftStartMinutes,
        $shiftEndMinutes,
        $midMinutes
    ) {
        if ((int)$period === 1) {
            return [$shiftStartMinutes, $midMinutes];
        }

        if ((int)$period === 2) {
            return [$midMinutes, $shiftEndMinutes];
        }

        return [$shiftStartMinutes, $shiftEndMinutes];
    };

    $overlaps = function ($start1, $end1, $start2, $end2) {
        return $start1 < $end2 && $end1 > $start2;
    };

    $stmt = $pdo->prepare("
        SELECT id, req_type, from_date, to_date, leave_period
        FROM regularization
        WHERE staff_profile_id = :staff_profile_id
          AND req_type IN (1, 2, 3)
          AND id != :hidden_id
          AND from_date <= :to_date
          AND to_date >= :from_date
          AND status IN (1, 0)
    ");

    $stmt->execute([
        ':staff_profile_id' => $staff_profile_id,
        ':hidden_id' => $hidden_id,
        ':from_date' => $newStart->format('Y-m-d 00:00:00'),
        ':to_date' => $newEnd->format('Y-m-d 23:59:59')
    ]);

    $existingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($existingRequests as $existing) {
        $existingType = (int)$existing['req_type'];

        $existingStart = new DateTime($existing['from_date']);
        $existingEnd = new DateTime($existing['to_date']);

        $overlapStart = $newStart > $existingStart
            ? clone $newStart
            : clone $existingStart;

        $overlapEnd = $newEnd < $existingEnd
            ? clone $newEnd
            : clone $existingEnd;

        $checkDate = new DateTime($overlapStart->format('Y-m-d'));
        $lastDate = new DateTime($overlapEnd->format('Y-m-d'));

        while ($checkDate <= $lastDate) {
            $date = $checkDate->format('Y-m-d');

            $existingIsLeaveOrWeekOff = in_array(
                $existingType,
                [1, 3],
                true
            );

            $newIsLeaveOrWeekOff = in_array(
                $req_type,
                [1, 3],
                true
            );

            // Existing Leave/Week Off versus new Leave/Week Off.
            if (
                $existingIsLeaveOrWeekOff &&
                $newIsLeaveOrWeekOff
            ) {
                $existingDateStart = $existingStart->format('Y-m-d');
                $existingDateEnd = $existingEnd->format('Y-m-d');

                if (
                    $date >= $existingDateStart &&
                    $date <= $existingDateEnd
                ) {
                    $message = 'A request already exists for this date. Please cancel the existing request before applying again.';

                    if ($existingType === 1) {
                        $message = 'Leave is already applied for this date. Please cancel the existing leave before applying leave or week off.';
                    } elseif ($existingType === 3) {
                        $message = 'Week off is already applied for this date. Please cancel the existing week off before applying leave or week off.';
                    }

                    echo json_encode([
                        'result' => '5',
                        'message' => $message
                    ]);
                    exit;
                }
            }

            // Existing Permission versus new Leave/Week Off.
            if (
                $existingType === 2 &&
                $newIsLeaveOrWeekOff
            ) {
                [$periodStart, $periodEnd] = $getPeriod($leave_period);

                $permissionStart = $date === $existingStart->format('Y-m-d')
                    ? $timeToMinutes($existing['from_date'])
                    : 0;

                $permissionEnd = $date === $existingEnd->format('Y-m-d')
                    ? $timeToMinutes($existing['to_date'])
                    : 1440;

                if ($overlaps(
                    $permissionStart,
                    $permissionEnd,
                    $periodStart,
                    $periodEnd
                )) {
                    $requestName = $req_type === 1 ? 'leave' : 'week off';

                    echo json_encode([
                        'result' => '5',
                        'message' => 'Permission is already present during this ' . $requestName . ' period. Please cancel the existing permission before applying ' . $requestName . '.'
                    ]);
                    exit;
                }
            }

            // Existing Leave/Week Off versus new Permission.
            if (
                $existingIsLeaveOrWeekOff &&
                $req_type === 2
            ) {
                [$periodStart, $periodEnd] = $getPeriod(
                    (int)$existing['leave_period']
                );

                $permissionStart = $date === $newStart->format('Y-m-d')
                    ? $timeToMinutes($from_date)
                    : 0;

                $permissionEnd = $date === $newEnd->format('Y-m-d')
                    ? $timeToMinutes($to_date)
                    : 1440;

                if ($overlaps(
                    $permissionStart,
                    $permissionEnd,
                    $periodStart,
                    $periodEnd
                )) {
                    $requestName = $existingType === 1 ? 'leave' : 'week off';

                    echo json_encode([
                        'result' => '5',
                        'message' => ucfirst($requestName) . ' is already applied during this permission period. Please cancel the existing ' . $requestName . ' before applying permission.'
                    ]);
                    exit;
                }
            }

            $checkDate->modify('+1 day');
        }
    }

    echo json_encode(['result' => '1']);

} catch (Throwable $e) {
    error_log($e->getMessage());

    echo json_encode([
        'result' => '2',
        'message' => 'Unable to validate existing regularization requests.'
    ]);
}
?>