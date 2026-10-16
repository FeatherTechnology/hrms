
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
    if (in_array($req_type, [3, 4], true)) {
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

    if ($req_type === 1 && !in_array($leave_period, [1, 2, 3], true)) {
        echo json_encode([
            'result' => '2',
            'message' => 'Please select a valid leave period.'
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

    $getLeavePeriod = function ($period) use (
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
          AND req_type IN (1, 2)
          AND id != :hidden_id
          AND from_date <= :to_date
          AND to_date >= :from_date
          AND status IN (1,0)
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

            // Existing permission versus new leave.
            if ($existingType === 2 && $req_type === 1) {
                [$leaveStart, $leaveEnd] = $getLeavePeriod($leave_period);

                $permissionStart = $date === $existingStart->format('Y-m-d')
                    ? $timeToMinutes($existing['from_date'])
                    : 0;

                $permissionEnd = $date === $existingEnd->format('Y-m-d')
                    ? $timeToMinutes($existing['to_date'])
                    : 1440;

                if ($overlaps(
                    $permissionStart,
                    $permissionEnd,
                    $leaveStart,
                    $leaveEnd
                )) {
                    echo json_encode([
                        'result' => '5',
                        'message' => 'Permission is already present for this leave period. Please cancel the existing permission before applying leave.'
                    ]);
                    exit;
                }
            }

            // Existing leave versus new permission.
            if ($existingType === 1 && $req_type === 2) {
                [$leaveStart, $leaveEnd] = $getLeavePeriod(
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
                    $leaveStart,
                    $leaveEnd
                )) {
                    echo json_encode([
                        'result' => '5',
                        'message' => 'Leave is already present for this permission period. Please cancel the existing leave before applying permission.'
                    ]);
                    exit;
                }
            }
            // Existing leave versus new leave on the same date.
            if ($existingType === 1 && $req_type === 1) {
                echo json_encode([
                    'result' => '5',
                    'message' => 'Leave is already applied for this date. Please cancel the existing leave before applying another leave.'
                ]);
                exit;
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
