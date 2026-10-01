<?php

require '../../ajaxconfig.php';

$response = [];

try {

    $company_id = $_POST['company_id'] ?? '';
    $shift_id   = $_POST['shift_id'] ?? '';
    $staff_id   = $_POST['staff_id'] ?? '';
    $date       = $_POST['date'] ?? '';
    $type       = $_POST['type'] ?? 'attendance';

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

    /*
    IMPORTANT:
    Do NOT require exit time here.
    Employee can have only entry time.In that case Working Hours will continue until shift end.
    Attendance Chart:updated_time -> entry_time
    Monitoring Chart:entry_time -> updated_time
    */

    if (!empty($date)) {

        if ($type == 'attendance') {

            $where[] = "DATE( COALESCE( NULLIF(a.updated_time, '0000-00-00 00:00:00'), a.entry_time)) = :date ";
        } else {
            $where[] = "  DATE(  COALESCE( NULLIF(a.entry_time, '0000-00-00 00:00:00'), a.updated_time)) = :date ";
        }

        $params[':date'] = $date;
    }

    $where_sql = '';

    if (!empty($where)) {
        $where_sql = "WHERE " . implode(' AND ', $where);
    }

    // MAIN ATTENDANCE QUERY
    $query = "SELECT
        a.staff_profile_id,
        st.staff_name,
        oi.shift,
        sc.shift_name,
        sc.start_time,
        sc.end_time,
        sc.grace_time,

        a.entry_time,
        a.updated_time,

        a.exit_time,
        a.updated_exit_time

    FROM attendance a

    LEFT JOIN staff_creation st
        ON st.id = a.staff_profile_id

    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = a.staff_profile_id
        )

    LEFT JOIN shift_creation sc
        ON sc.id = oi.shift

    $where_sql

    ORDER BY " . (
        $type == 'attendance'
        ? "COALESCE(NULLIF(a.updated_time, '0000-00-00 00:00:00'), a.entry_time)"
        : "COALESCE(NULLIF(a.entry_time, '0000-00-00 00:00:00'), a.updated_time)"
    ) . " ASC
    ";

    $stmt = $pdo->prepare($query);

    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {

        // ENTRY TIME

        if ($type == 'attendance') {
          //  Attendance Chart: Updated entry has priority.
            $attendance_time =  (!empty($row['updated_time']) &&  $row['updated_time'] !== '0000-00-00 00:00:00') ? $row['updated_time'] : $row['entry_time'];
        } else {
         // Monitoring Chart: Original entry has priority
            $attendance_time =( !empty($row['entry_time']) && $row['entry_time'] !== '0000-00-00 00:00:00') ? $row['entry_time'] : $row['updated_time'];
        }

        if (   empty($attendance_time) ||   $attendance_time === '0000-00-00 00:00:00' ) {
            continue;
        }
        $entry_time = strtotime($attendance_time);
        // EXIT TIME
        /*
        Attendance Chart:updated_exit_time -> exit_time
        Monitoring Chart:exit_time -> updated_exit_time
        0000-00-00 00:00:00 is treated as NO EXIT.
        */
        $exit_time = null;
        if ($type == 'attendance') {
            // Attendance Chart
            if (!empty($row['updated_exit_time']) && $row['updated_exit_time'] !== '0000-00-00 00:00:00') {
                $exit_time = strtotime($row['updated_exit_time']);
            } elseif (!empty($row['exit_time']) && $row['exit_time'] !== '0000-00-00 00:00:00') {
                $exit_time = strtotime($row['exit_time']);
            }
        } else {
            // Monitoring Chart
            if ( !empty($row['exit_time']) && $row['exit_time'] !== '0000-00-00 00:00:00' ) {
                $exit_time = strtotime($row['exit_time']);
            } elseif (!empty($row['updated_exit_time']) && $row['updated_exit_time'] !== '0000-00-00 00:00:00' ) {
                $exit_time = strtotime($row['updated_exit_time']);
            }
        }
        // SHIFT START / END
     
        $shift_start = strtotime( date('Y-m-d', $entry_time) . ' ' . $row['start_time'] );
        $shift_end = strtotime( date('Y-m-d', $entry_time) . ' ' . $row['end_time'] );
    
        // NIGHT SHIFT SUPPORT
        if ($shift_end <= $shift_start) {
             $shift_end = strtotime('+1 day', $shift_end);
        }
        // SHIFT MIDPOINT
        $shift_duration = $shift_end - $shift_start;
        $mid_shift = $shift_start + ($shift_duration / 2);
        // GRACE TIME
      
        $grace_minutes = 0;

        if (!empty($row['grace_time'])) {
            preg_match('/\d+/', $row['grace_time'], $match);
            $grace_minutes = isset($match[0]) ? (int)$match[0]: 0;
        }
        // EMPLOYEE WORKING START
        $working_start = max(  $entry_time,  $shift_start );
        // ADVANCE ATTENDANCE
        if ($entry_time < $shift_start) {

            $response[] = [
                'staff_name' => $row['staff_name'],
                'type'       => 'Advance Attendance',
                'color'      => '#c4f3bf',
                'start'      => date('Y-m-d H:i:s',$entry_time),
                'end'        => date('Y-m-d H:i:s', $shift_start)
            ];
        }
        // ARRAYS
      
        $permissions = [];
        $blockedTimes = [];
       
        // GET REGULARIZATION
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
            AND DATE(r.from_date) = :att_date
            ORDER BY r.from_date ASC
        ");

        $regQry->execute([':staff_id' => $row['staff_profile_id'],':att_date' => date('Y-m-d', $entry_time ) ]);
        $regularizations = $regQry->fetchAll( PDO::FETCH_ASSOC);
        $leaveStart = null;
        $leaveEnd   = null;
        $leaveName  = '';

        foreach ($regularizations as $reg) {
            // PERMISSION

            if ( $reg['req_type'] == 2 && !empty($reg['from_date']) && !empty($reg['to_date'])) {
                $permissionStart = strtotime($reg['from_date']);
                $permissionEnd = strtotime($reg['to_date']);
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
            // LEAVE

            if (in_array($reg['req_type'], [1, 3])) {
                if ($reg['req_type'] == 1) {
                    $leaveName =  !empty($reg['leave_name'])  ? $reg['leave_name']   : 'Leave';
                } else {
                    $leaveName = 'Week Off';
                }
                switch ($reg['leave_period']) {
                    // First Half
                    case 1:
                        $leaveStart = $shift_start;
                        $leaveEnd = $mid_shift;
                        break;
                    // Second Half
                    case 2:
                        $leaveStart = $mid_shift;
                        $leaveEnd = $shift_end;
                        break;
                    // Full Day
                    case 3:
                        $leaveStart = $shift_start;
                        $leaveEnd = $shift_end;
                        break;
                }
                if ($leaveStart && $leaveEnd) {
                    $blockedTimes[] = [
                        'start' => $leaveStart,
                        'end'   => $leaveEnd
                    ];
                    $response[] = [
                        'staff_name' => $row['staff_name'],
                        'type' => $leaveName,
                        'color' => ( $reg['req_type'] == 1) ? '#02756a' : '#9E9E9E',
                        'start' => date('Y-m-d H:i:s',$leaveStart),
                        'end' => date('Y-m-d H:i:s',$leaveEnd)
                    ];
                }
            }
        }
        // SORT BLOCKED TIMES
        usort(
            $blockedTimes,
            function ($a, $b) {
                return $a['start'] <=> $b['start'];

            }
        );
        // EFFECTIVE SHIFT START
        $effectiveShiftStart = $shift_start;
        if (  $leaveStart &&  $leaveEnd &&  $leaveStart == $shift_start ) {
            $effectiveShiftStart = $leaveEnd;
        }
        // GRACE
        $grace_end = strtotime( '+' . $grace_minutes . ' minutes', $effectiveShiftStart );
        if ($grace_minutes > 0 && $entry_time > $effectiveShiftStart) {
            $grace_bar_end = min(  $entry_time,  $grace_end);
            if ($grace_bar_end > $effectiveShiftStart) {
                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Grace Time',
                    'color'      => '#9C27B0',
                    'start'      => date(
                        'Y-m-d H:i:s',
                        $effectiveShiftStart
                    ),
                    'end'        => date(
                        'Y-m-d H:i:s',
                        $grace_bar_end
                    )
                ];
            }
        }
        // LATE ENTRY
        $lateStart = $grace_end;
        foreach ($permissions as $permission) {
            // Permission covers entry
            if ( $permission['start'] <= $entry_time &&  $permission['end'] >= $entry_time ) {
                $lateStart = $permission['end'];
                break;
            }
            // Entry after permission
            if ( $permission['start'] <= $entry_time && $permission['end'] < $entry_time) {
                $lateStart = $permission['end'];
            }
        }

        if ($entry_time > $lateStart) {
            $response[] = [
                'staff_name' => $row['staff_name'],
                'type'       => 'Late Entry',
                'color'      => '#f75d52',
                'start'      => date(
                    'Y-m-d H:i:s',
                    $lateStart
                ),
                'end'        => date(
                    'Y-m-d H:i:s',
                    $entry_time
                )
            ];
        }

        // EXIT STATU
        /*
        IMPORTANT:

        No exit:
            Working Hours -> shift end

        Exit before shift end:
            Working Hours -> exit
            Early Exit -> exit to shift end (RED)

        Exit after shift end:
            Working Hours -> shift end
            Late Exit -> shift end to exit (LIGHT GREEN)

        Exit exactly at shift end:
            Working Hours -> shift end
            No Early/Late Exit
        */
        if ($exit_time !== null) {
            // EARLY EXIT
            if ($exit_time < $shift_end) {
                $working_end = max(
                    $working_start,
                    $exit_time
                );
                // WORKING HOURS
                if (  $currentStart = $working_start ) {
                    if ($currentStart < $working_end) {
                        if (empty($blockedTimes)) {

                            $response[] = [
                                'staff_name' => $row['staff_name'],
                                'type'       => 'Working Hours',
                                'color'      => '#4CAF50',
                                'start'      => date(
                                    'Y-m-d H:i:s',
                                    $currentStart
                                ),
                                'end'        => date(
                                    'Y-m-d H:i:s',
                                    $working_end
                                )
                            ];

                        } else {
                            foreach ($blockedTimes as $block) {
                                if (
                                    $block['end'] <= $shift_start ||
                                    $block['start'] >= $working_end
                                ) {
                                    continue;
                                }

                                $blockStart = max($block['start'],$shift_start);
                                $blockEnd = min( $block['end'], $working_end );

                                if ($currentStart < $blockStart) {
                                    $response[] = [
                                        'staff_name' =>  $row['staff_name'],
                                        'type' =>  'Working Hours',
                                        'color' =>'#4CAF50',
                                        'start' => date(
                                            'Y-m-d H:i:s',
                                            $currentStart
                                        ),

                                        'end' => date(
                                            'Y-m-d H:i:s',
                                            $blockStart
                                        )
                                    ];
                                }
                                if ($currentStart < $blockEnd) {
                                    $currentStart = $blockEnd;
                                }
                            }
                            if ($currentStart < $working_end) {
                                $response[] = [
                                    'staff_name' =>  $row['staff_name'],
                                    'type' =>  'Working Hours',
                                    'color' =>  '#4CAF50',
                                    'start' => date(
                                        'Y-m-d H:i:s',
                                        $currentStart
                                    ),
                                    'end' => date(
                                        'Y-m-d H:i:s',
                                        $working_end
                                    )
                                ];
                            }
                        }
                    }
                }
                // EARLY EXIT RED BAR
                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Early Exit',
                    'color'      => '#f44336',
                    'start'      => date(
                        'Y-m-d H:i:s',
                        $exit_time
                    ),
                    'end'        => date(
                        'Y-m-d H:i:s',
                        $shift_end
                    )
                ];

            } elseif ($exit_time > $shift_end) {
                // NORMAL WORKING HOURS UNTIL SHIFT END
              
                $working_end = $shift_end;
                if ($working_start < $working_end) {
                    if (empty($blockedTimes)) {
                        $response[] = [
                            'staff_name' => $row['staff_name'],
                            'type' => 'Working Hours',
                            'color' =>'#4CAF50',
                            'start' => date('Y-m-d H:i:s', $working_start),
                            'end' => date(
                                'Y-m-d H:i:s', $working_end
                            )
                        ];

                    } else {
                        $currentStart = $working_start;
                        foreach ($blockedTimes as $block) {
                            if ( $block['end'] <= $shift_start || $block['start'] >= $working_end ) {
                                continue;
                            }

                            $blockStart = max( $block['start'], $shift_start );
                            $blockEnd = min($block['end'],$working_end);
                            if ( $currentStart < $blockStart  ) {
                                $response[] = [
                                    'staff_name' =>$row['staff_name'],
                                    'type' =>'Working Hours',
                                    'color' =>'#4CAF50',
                                    'start' => date(
                                        'Y-m-d H:i:s',
                                        $currentStart
                                    ),
                                    'end' => date(
                                        'Y-m-d H:i:s',
                                        $blockStart
                                    )
                                ];
                            }

                            if (  $currentStart < $blockEnd ) {
                                $currentStart = $blockEnd;}
                        }

                        if ($currentStart < $working_end) {
                            $response[] = [
                                'staff_name' => $row['staff_name'],
                                'type' => 'Working Hours',
                                'color' => '#4CAF50',
                                'start' => date(
                                    'Y-m-d H:i:s',
                                    $currentStart
                                ),

                                'end' => date(
                                    'Y-m-d H:i:s',
                                    $working_end
                                )
                            ];
                        }
                    }
                }
                // LATE EXIT / OT LIGHT GREEN
                $response[] = [
                    'staff_name' => $row['staff_name'],
                    'type'       => 'Late Exit',
                    'color'      => '#c4f3bf',
                    'start'      => date(
                        'Y-m-d H:i:s',
                        $shift_end
                    ),
                    'end'        => date(
                        'Y-m-d H:i:s',
                        $exit_time
                    )
                ];


            } else {
                // EXIT EXACTLY AT SHIFT END
                $working_end = $shift_end;
                if ($working_start < $working_end) {
                    if (empty($blockedTimes)) {
                        $response[] = [ 'staff_name' => $row['staff_name'],
                            'type' =>'Working Hours',
                            'color' =>'#4CAF50',
                            'start' => date(
                                'Y-m-d H:i:s',
                                $working_start
                            ),
                            'end' => date(
                                'Y-m-d H:i:s',
                                $working_end
                            )
                        ];
                    } else {
                        $currentStart = $working_start;
                        foreach ($blockedTimes as $block) {
                            if (
                                $block['end'] <= $shift_start ||
                                $block['start'] >= $working_end
                            ) {
                                continue;
                            }
                            $blockStart = max($block['start'],$shift_start
                            );
                            $blockEnd = min( $block['end'], $working_end
                            );
                            if ($currentStart < $blockStart) {
                                $response[] = [
                                    'staff_name' =>  $row['staff_name'],
                                    'type' =>'Working Hours',
                                    'color' => '#4CAF50',
                                    'start' => date( 'Y-m-d H:i:s', $currentStart),
                                    'end' => date( 'Y-m-d H:i:s', $blockStart)
                                ];
                            }
                            if ($currentStart < $blockEnd) {
                                $currentStart =$blockEnd;
                            }
                        }
                        if ($currentStart < $working_end) {
                            $response[] = [
                                'staff_name' => $row['staff_name'],
                                'type' => 'Working Hours',
                                'color' => '#4CAF50',
                                'start' => date( 'Y-m-d H:i:s',$currentStart),
                                'end' => date('Y-m-d H:i:s', $working_end)
                            ];
                        }
                    }
                }
            }

        } else {
            // NO EXIT TIME
            // Employee has only entry time.Keep Working Hours until shift end.
            $working_end = $shift_end;
            if ($working_start < $working_end) {
                if (empty($blockedTimes)) {
                    $response[] = [
                        'staff_name' => $row['staff_name'],
                        'type'       => 'Working Hours',
                        'color'      => '#4CAF50',
                        'start'      => date( 'Y-m-d H:i:s', $working_start ),
                        'end'        => date('Y-m-d H:i:s',$working_end)
                    ];

                } else {
                    $currentStart = $working_start;
                    foreach ($blockedTimes as $block) {
                        if (   $block['end'] <= $shift_start || $block['start'] >= $working_end) {
                            continue;
                        }
                        $blockStart = max(  $block['start'],  $shift_start);
                        $blockEnd = min( $block['end'], $working_end );
                        if ($currentStart < $blockStart) {
                            $response[] = ['staff_name' =>    $row['staff_name'],
                                'type' =>  'Working Hours',
                                'color' =>  '#4CAF50',
                                'start' => date(  'Y-m-d H:i:s',  $currentStart ),
                                'end' => date( 'Y-m-d H:i:s', $blockStart)
                            ];
                        }

                        if ($currentStart < $blockEnd) {
                            $currentStart =  $blockEnd;
                        }
                    }
                    if ($currentStart < $working_end) {
                        $response[] = [
                            'staff_name' => $row['staff_name'],
                            'type' => 'Working Hours',
                            'color' => '#4CAF50',
                            'start' => date( 'Y-m-d H:i:s',$currentStart),
                            'end' => date('Y-m-d H:i:s',$working_end)          
                        ];
                    }
                }
            }
        }
    }
    // OT
    $otWhere = [];
    $otParams = [  ':date' => $date  ];
    if (!empty($company_id)) {
        $otWhere[] = "st.company_id = :company_id";
        $otParams[':company_id'] = $company_id;
    }

    if (!empty($shift_id)) {
        $otWhere[] = "oi.shift = :shift_id";
        $otParams[':shift_id'] = $shift_id;
    }

    if (!empty($staff_id) && $staff_id != 'all') {
        $otWhere[] = "st.id = :staff_id";
        $otParams[':staff_id'] = $staff_id;
    }

    $otWhereSql = !empty($otWhere)? " AND " . implode(" AND ",$otWhere  ): "";
    $otQry = $pdo->prepare("SELECT
        st.staff_name,
        r.from_date,
        r.to_date
        FROM regularization r
        LEFT JOIN staff_creation st ON st.id = r.staff_profile_id
        LEFT JOIN occupation_info oi
            ON oi.id = (
                SELECT MAX(id)
                FROM occupation_info
                WHERE staff_profile_id = st.id
            )
        WHERE r.req_type = 4
        AND r.status = 1
        AND DATE(r.from_date) = :date
        $otWhereSql
    ");

    $otQry->execute($otParams);
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

    // LEAVE WITHOUT ATTENDANCE
    $leaveWhere = [];
    $leaveParams = [ ':date' => $date ];
    if (!empty($company_id)) {
        $leaveWhere[] = "st.company_id = :company_id";
        $leaveParams[':company_id'] = $company_id;
    }
    if (!empty($shift_id)) {
        $leaveWhere[] = "oi.shift = :shift_id";
        $leaveParams[':shift_id'] =  $shift_id;
    }
    if (!empty($staff_id) && $staff_id != 'all') {
        $leaveWhere[] = "st.id = :staff_id";
        $leaveParams[':staff_id'] =$staff_id;
    }
    $leaveWhereSql = !empty($leaveWhere) ? " AND " . implode( " AND ",  $leaveWhere ) : "";


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
        LEFT JOIN staff_creation st ON st.id = r.staff_profile_id
        LEFT JOIN occupation_info oi
            ON oi.id = (
                SELECT MAX(id)
                FROM occupation_info
                WHERE staff_profile_id = r.staff_profile_id
            )
        LEFT JOIN shift_creation sc ON sc.id = oi.shift
        LEFT JOIN leave_creation lc ON lc.id = r.leave_type
        WHERE r.req_type IN (1,3) AND r.status = 1
        AND :date BETWEEN DATE(r.from_date) AND DATE(r.to_date)
        $leaveWhereSql
        AND NOT EXISTS (SELECT 1 FROM attendance a WHERE a.staff_profile_id =r.staff_profile_id AND DATE( COALESCE(  a.entry_time,a.updated_time)) = :date)
    ");

    $leaveQry->execute($leaveParams);
    $leaveResult = $leaveQry->fetchAll(  PDO::FETCH_ASSOC);
    foreach ($leaveResult as $leaveRow) {
        $shift_start = strtotime(  $date . ' ' . $leaveRow['start_time'] );
        $shift_end = strtotime(   $date . ' ' . $leaveRow['end_time'] );
        if ($shift_end <= $shift_start) {
            $shift_end = strtotime( '+1 day', $shift_end);
        }
        $mid_shift = $shift_start + (($shift_end - $shift_start) / 2 );
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
            'staff_name' =>  $leaveRow['staff_name'],
            'type' =>  ($leaveRow['req_type'] == 1)? $leaveRow['leave_name']  : 'Week Off',
            'color' => ($leaveRow['req_type'] == 1) ? '#02756a' : '#9E9E9E',
            'start' => date( 'Y-m-d H:i:s', $leaveStart),
            'end' => date( 'Y-m-d H:i:s',$leaveEnd)
        ];
        // REMAINING SHIFT -> LOP
        if ($leaveRow['leave_period'] == 1) {
            $response[] = [
                'staff_name' => $leaveRow['staff_name'],
                'type' =>  'LOP',
                'color' => '#ff0000',
                'start' => date('Y-m-d H:i:s',$mid_shift),
                'end' => date('Y-m-d H:i:s',$shift_end)
            ];
        } elseif ($leaveRow['leave_period'] == 2) {
            $response[] = ['staff_name' =>     $leaveRow['staff_name'],
                'type' =>   'LOP',
                'color' =>  '#ff0000',
                'start' => date( 'Y-m-d H:i:s', $shift_start),
                'end' => date( 'Y-m-d H:i:s', $mid_shift )
            ];
        }
    }
    // LOP
    $lopWhere = [];
    $lopParams = [  ':date' => $date ];
    if (!empty($company_id)) {
        $lopWhere[] = "st.company_id = :company_id";
        $lopParams[':company_id'] = $company_id;
    }
    if (!empty($shift_id)) {
        $lopWhere[] ="oi.shift = :shift_id";
        $lopParams[':shift_id'] = $shift_id;
    }

    if (!empty($staff_id) && $staff_id != 'all') {
        $lopWhere[] =  "st.id = :staff_id";
        $lopParams[':staff_id'] = $staff_id;
    }

    $lopWhereSql = '';
    if (!empty($lopWhere)) {
        $lopWhereSql =  "AND " . implode( " AND ",$lopWhere );
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

        LEFT JOIN shift_creation sc ON sc.id = oi.shift
        WHERE 1 $lopWhereSql
        AND NOT EXISTS (
            SELECT 1
            FROM attendance a
            WHERE a.staff_profile_id = st.id
            AND DATE(COALESCE(a.entry_time, a.updated_time)) = :date )
        AND NOT EXISTS (
            SELECT 1
            FROM regularization r WHERE r.staff_profile_id = st.id
            AND r.req_type IN (1,3) AND r.status = 1
            AND :date BETWEEN DATE(r.from_date) AND DATE(r.to_date)
        )
    ");
    $lopQry->execute($lopParams);
    $lopResult = $lopQry->fetchAll( PDO::FETCH_ASSOC);
    foreach ($lopResult as $lopRow) {
        $shift_start = strtotime($date . ' ' . $lopRow['start_time']); 
        $shift_end = strtotime(  $date . ' ' . $lopRow['end_time'] );
        if ($shift_end <= $shift_start) { $shift_end = strtotime('+1 day', $shift_end);
        }
        $response[] = [ 'staff_name' => $lopRow['staff_name'],
            'type' =>   'LOP',
            'color' => '#ff0000',
            'start' => date('Y-m-d H:i:s',$shift_start ),
            'end' => date( 'Y-m-d H:i:s',  $shift_end )
        ];
    }
} catch (PDOException $e) {
    $response = [
        'status'  => false,
        'message' => $e->getMessage()
    ];
}
echo json_encode($response);