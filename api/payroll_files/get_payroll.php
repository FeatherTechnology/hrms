
<?php

// Generate monthly payroll report based on company, branch, and selected month.
// Keeps existing attendance, leave, LOP, OT, loan, advance and statutory conditions.

include '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$branch_id  = $_POST['branch_id'];
$dept_id  = $_POST['dept_id'];
$month      = $_POST['month'];

$stff_con = '';
$stff_ctc_con = '';

// In pay slip we use this condition to get the separate pay slip
if (isset($_POST['stf_prf_id']) && $_POST['stf_prf_id'] != '') {

    $stf_prf_id = $_POST['stf_prf_id'];

    $stff_con = "AND o.staff_profile_id = '$stf_prf_id'";

    $stff_ctc_con = "
        AND sci.staff_profile_id = '$stf_prf_id'
        AND sci.ctc_amount > 0
    ";
}

$result = array();

// ==========================================================
// PT HISTORY MODE / MONTHLY PT HELPER
// ==========================================================

$pt_history_mode = !empty($_POST['pt_history']) && $_POST['pt_history'] == 1;

if (!function_exists('getHistoricalPtEarnings')) {
    function getHistoricalPtEarnings($company_id, $branch_id,$dept_id, $history_month, $staff_profile_id)
    {
        $savedPost = $_POST;

        $_POST['company_id'] = $company_id;
        $_POST['branch_id']  = $branch_id;
        $_POST['dept_id']  = $dept_id;
        $_POST['month']      = $history_month;
        $_POST['pt_history'] = 1;
        $_POST['stf_prf_id'] = $staff_profile_id;

        ob_start();

        // The included copy runs in this function scope.
        // pt_history mode returns after calculating one month.
        include __FILE__;

        $output = ob_get_clean();

        $_POST = $savedPost;

        $decoded = json_decode($output, true);

        if (!is_array($decoded) || empty($decoded['data'])) {
            return 0;
        }

        foreach ($decoded['data'] as $row) {
            if (
                isset($row['staff_profile_id'])
                && (string)$row['staff_profile_id'] === (string)$staff_profile_id
            ) {
                return isset($row['pt_eligible_earnings'])
                    ? (float)str_replace(',', '', (string)$row['pt_eligible_earnings'])
                    : 0;
            }
        }

        return 0;
    }
}


// ==========================================================
// MONTH DETAILS
// ==========================================================

$month_start = $month . "-01";
$month_end   = date("Y-m-t", strtotime($month_start));

$total_days = (int)date("t", strtotime($month_start));


// ==========================================================
// CHECK PENDING REGULARIZATION BEFORE PAYROLL GENERATION
// ==========================================================

$pendingRegQry = $pdo->query("
    SELECT COUNT(*) AS pending_count
    FROM regularization
    WHERE company_id = '$company_id'
    AND branch_id = '$branch_id'
    AND dep_id = '$dept_id'
    AND status = 0
    AND (
        DATE(from_date) <= '$month_end'
        AND DATE(to_date) >= '$month_start'
    )
");

$pendingReg = $pendingRegQry->fetch();

if (!$pt_history_mode && $pendingReg['pending_count'] > 0) {

    echo json_encode([
        "status" => 0,
        "message" => "Please complete all regularization requests before generating payroll."
    ]);

    exit;
}


// ==========================================================
// GET ALL SALARY COMPONENTS
// ==========================================================

$componentArr = array();

$getComponents = $pdo->prepare("
    SELECT
        id,
        salary_component
    FROM ctc_creation
    WHERE company_id = ?
    AND (
        (
            status = 0
            AND effective_from <= ?
        )
        OR
        (
            status = 1
            AND effective_from > ?
        )
    )
");

$getComponents->execute([
    $company_id,
    $month_start,
    $month_start
]);

while ($row = $getComponents->fetch()) {

    $componentArr[] = array(
        'id'   => $row['id'],
        'name' => $row['salary_component']
    );
}


// ==========================================================
// GET STAFF LIST
// ==========================================================

$getStaff = $pdo->query("
    SELECT

        sc.id AS staff_profile_id,
        sc.staff_id,
        sc.staff_name,
        sc.staff_type,
        sc.pf_available,
        sc.esi_available,
        sc.pt_available,

        bc.branch_name,
        depcr.department_name AS department,
        descr.designation AS designation,
        tc.team_name AS team,
        cc.company_name,

        o.total_ctc,
        o.shift,
        o.ot_payment,
        o.ot_per_day

    FROM staff_creation sc

    LEFT JOIN occupation_info o
        ON o.id = (
            SELECT id
            FROM occupation_info
            WHERE staff_profile_id = sc.id
            AND (
                effective_from <= '$month_end'
                OR effective_from IS NULL
            )
            ORDER BY
                CASE
                    WHEN effective_from IS NULL THEN 0
                    ELSE 1
                END,
                effective_from DESC,
                id DESC
            LIMIT 1
        )

    LEFT JOIN company_creation cc
        ON cc.id = o.company_id

    LEFT JOIN branch_creation bc
        ON bc.id = o.branch_id

    LEFT JOIN department_creation depcr
        ON depcr.id = o.department

    LEFT JOIN designation_creation descr
        ON descr.id = o.designation

    LEFT JOIN team_name_creation tc
        ON tc.id = o.team

    WHERE
        o.company_id = '$company_id'
        AND o.branch_id = '$branch_id'
        AND o.department = '$dept_id'

        AND (
            sc.relieve_date IS NULL
            OR sc.relieve_date = ''
            OR STR_TO_DATE(sc.relieve_date,'%Y-%m-%d') >= '$month_end'
            OR DATE_FORMAT(
                STR_TO_DATE(sc.relieve_date,'%Y-%m-%d'),
                '%Y-%m'
            ) = '$month'
        )

        AND (
            sc.joining_date IS NULL
            OR sc.joining_date = ''
            OR sc.joining_date <= '$month_end'
        )

        $stff_con
");

$sno = 1;


// ==========================================================
// STAFF LOOP
// ==========================================================

while ($staff = $getStaff->fetch()) {

    $staff_profile_id = $staff['staff_profile_id'];

    $components = array();

    $gross_total = 0;

    // Earned Basic + DA used only for PF calculation
    $pf_wages = 0;

    // PT eligible monthly earnings.
    // Salary earnings are included; Bonus and Gratuity are excluded.
    // OT and extra-working earnings are included.
    $pt_eligible_earnings = 0;


    // ======================================================
    // WEEK OFF
    // ======================================================

    $weekoffQry = $pdo->query("
        SELECT cw.week_off
        FROM company_weekoffs cw
        WHERE cw.company_id = '$company_id'
    ");

    $weekoff = $weekoffQry->fetch()['week_off'] ?? 0;


    // ======================================================
    // HOLIDAYS
    // ======================================================

    $holidayQry = $pdo->query("
        SELECT IFNULL(SUM(no_of_days),0) AS total_holidays
        FROM holiday_creation
        WHERE company_id = '$company_id'
        AND status = 0
        AND (
            from_date BETWEEN '$month_start' AND '$month_end'
            OR to_date BETWEEN '$month_start' AND '$month_end'
        )
    ");

    $total_holidays = $holidayQry->fetch()['total_holidays'] ?? 0;


    // ======================================================
    // WORKING DAYS
    // ======================================================

    $working_days = $total_days - $weekoff - $total_holidays;

    if ($working_days < 0) {
        $working_days = 0;
    }


    // ======================================================
    // ONE DAY SALARY
    // ======================================================

    $total_ctc = floatval(
        str_replace(',', '', $staff['total_ctc'])
    );

    $one_day_salary = 0;

    if ($total_days > 0 && $working_days > 0) {
        $one_day_salary = $total_ctc / $working_days;
    }


    // ======================================================
    // SHIFT DETAILS
    // ======================================================

    $shift_hours = 0;

    $shiftQry = $pdo->query("
        SELECT shift_time
        FROM shift_creation
        WHERE id = '" . $staff['shift'] . "'
    ");

    $shiftData = $shiftQry->fetch();

    if ($shiftData) {
        preg_match('/(\d+)/', $shiftData['shift_time'], $m);
        $shift_hours = $m[1] ?? 0;
    }


    // ======================================================
    // GET SHIFT START / END / GRACE
    // ======================================================

    $shiftQry = $pdo->query("
        SELECT
            start_time,
            end_time,
            grace_time
        FROM shift_creation
        WHERE id = '" . $staff['shift'] . "'
    ");

    $shift = $shiftQry->fetch();

    $present_days = 0;

    // Exit-time based LOP
    $exit_lop_days = 0;

    // Attendance deduction
    $total_attendance_deduction = 0;


    // ======================================================
    // ATTENDANCE CALCULATION
    // ======================================================

    if ($shift) {

        $attQry = $pdo->query("
            SELECT

                DATE(
                    COALESCE(updated_time, entry_time)
                ) AS att_date,

                COALESCE(
                    updated_time,
                    entry_time
                ) AS entry_time,

                COALESCE(
                    updated_exit_time,
                    exit_time
                ) AS exit_time,

                deduction_amount

            FROM attendance

            WHERE staff_profile_id = '$staff_profile_id'

            AND DATE(
                COALESCE(updated_time, entry_time)
            ) BETWEEN '$month_start' AND '$month_end'
        ");

        while ($att = $attQry->fetch()) {

            $shift_start = strtotime(
                $att['att_date'] . " " . $shift['start_time']
            );

            $shift_end = strtotime(
                $att['att_date'] . " " . $shift['end_time']
            );

            // GRACE TIME
            $grace_minutes = intval($shift['grace_time']);

            // ALLOWED ENTRY TIME
            $allowed_entry_time =
                $shift_start + ($grace_minutes * 60);

            // SECOND HALF START
            $second_half_start =
                $shift_start +
                (($shift_end - $shift_start) / 2);

            // SECOND HALF ALLOWED TIME
            $second_half_allowed_time =
                $second_half_start +
                ($grace_minutes * 60);

            // EMPLOYEE ENTRY TIME
            $entry_time = strtotime($att['entry_time']);

            // ENTRY BASED PRESENT
            $entry_present = 0;

            // FULL DAY
            if ($entry_time <= $allowed_entry_time) {
                $entry_present = 1;
            }

            // HALF DAY
            elseif ($entry_time <= $second_half_allowed_time) {
                $entry_present = 0.5;
            }

            // FULL LOP
            else {
                $entry_present = 0;
            }


            // ==================================================
            // EARLY EXIT LOP
            // ==================================================

            $early_exit_lop = 0;

            if (!empty($att['exit_time'])) {

                $exit_time = strtotime($att['exit_time']);

                if ($exit_time < $shift_end) {

                    // CHECK APPROVED PERMISSION
                    $permission_exists = false;

                    $permissionQry = $pdo->query("
                        SELECT
                            from_date,
                            to_date
                        FROM regularization

                        WHERE staff_profile_id = '$staff_profile_id'

                        AND req_type = 2
                        AND status = 1

                        AND DATE(from_date)
                            <= '" . $att['att_date'] . "'

                        AND DATE(to_date)
                            >= '" . $att['att_date'] . "'
                    ");

                    while ($permission = $permissionQry->fetch()) {

                        $permission_start = strtotime(
                            $permission['from_date']
                        );

                        $permission_end = strtotime(
                            $permission['to_date']
                        );

                        if (
                            $permission_start <= $exit_time
                            &&
                            $permission_end >= $shift_end
                        ) {
                            $permission_exists = true;
                            break;
                        }
                    }

                    // NO PERMISSION
                    if (!$permission_exists) {

                        // Half-day cutoff
                        $half_day_exit_cutoff =
                            $shift_end - (3 * 60 * 60);

                        // FULL DAY LOP
                        if ($exit_time < $half_day_exit_cutoff) {
                            $early_exit_lop = 1;
                        }

                        // HALF DAY LOP
                        else {
                            $early_exit_lop = 0.5;
                        }
                    }
                }
            }


            // ==================================================
            // COMBINE ENTRY + EXIT LOP
            // ==================================================

            if ($entry_present == 0) {

                $present_for_day = 0;

            } elseif ($entry_present == 0.5) {

                if ($early_exit_lop >= 1) {
                    $present_for_day = 0;
                } else {
                    $present_for_day = 0.5;
                }

            } else {

                if ($early_exit_lop >= 1) {
                    $present_for_day = 0;
                } elseif ($early_exit_lop == 0.5) {
                    $present_for_day = 0.5;
                } else {
                    $present_for_day = 1;
                }
            }

            // ADD PRESENT DAYS
            $present_days += $present_for_day;

            // ADD EXIT LOP
            $exit_lop_days += $early_exit_lop;

            // ATTENDANCE DEDUCTION
            $total_attendance_deduction +=
                floatval($att['deduction_amount']);
        }
    }


    // ======================================================
    // APPROVED LEAVE
    // ======================================================

    $leaveQry = $pdo->query("
        SELECT
            from_date,
            to_date,
            leave_period

        FROM regularization

        WHERE staff_profile_id = '$staff_profile_id'

        AND req_type = 1
        AND status = 1

        AND DATE(from_date) <= '$month_end'
        AND DATE(to_date) >= '$month_start'
    ");

    $approved_leave = 0;

    while ($leave = $leaveQry->fetch()) {

        $s = strtotime($leave['from_date']);
        $e = strtotime($leave['to_date']);

        $ms = strtotime($month_start);
        $me = strtotime($month_end);

        $a = max($s, $ms);
        $b = min($e, $me);

        if ($b >= $a) {

            if (
                $leave['leave_period'] == 1
                ||
                $leave['leave_period'] == 2
            ) {
                $approved_leave += 0.5;
            } else {
                $approved_leave += (($b - $a) / 86400) + 1;
            }
        }
    }


    // ======================================================
    // TOTAL PAYABLE DAYS
    // ======================================================

    $total_payable_days =
        $present_days + $approved_leave;


    // ======================================================
    // EXTRA WORKING DAYS
    // ======================================================

    $extra_working_days = 0;

    if ($total_payable_days > $working_days) {
        $extra_working_days =
            $total_payable_days - $working_days;
    }


    // ======================================================
    // LOP DAYS
    // ======================================================

    $lop_days = 0;

    if ($total_payable_days < $working_days) {
        $lop_days =
            $working_days - $total_payable_days;
    }


    // ======================================================
    // LOP AMOUNT
    // ======================================================

    $lop_amount =
        $lop_days * $one_day_salary;


    // ======================================================
    // EARNED FOR MONTH
    // ======================================================

    $earned_for_month =
        $total_ctc - $lop_amount;

    if ($earned_for_month < 0) {
        $earned_for_month = 0;
    }


    // ======================================================
    // EXTRA WORKING AMOUNT
    // ======================================================

    $extra_working_amount =
        $extra_working_days * $one_day_salary;


    // ======================================================
    // OT CALCULATION
    // ======================================================

    $otQry = $pdo->query("
        SELECT
            from_date,
            to_date

        FROM regularization

        WHERE staff_profile_id = '$staff_profile_id'

        AND req_type = 4
        AND status = 1

        AND DATE(from_date) <= '$month_end'
        AND DATE(to_date) >= '$month_start'
    ");

    $total_ot_minutes = 0;

    while ($ot = $otQry->fetch()) {

        $ot_start = strtotime($ot['from_date']);
        $ot_end   = strtotime($ot['to_date']);

        if ($ot_end > $ot_start) {
            $total_ot_minutes += ($ot_end - $ot_start) / 60;
        }
    }

    $total_ot_hours = $total_ot_minutes / 60;

    $ot_days = ($shift_hours > 0)
        ? ($total_ot_hours / $shift_hours)
        : 0;

    $ot_hours_text =
        floor($total_ot_hours)
        . " hrs "
        . ((int)$total_ot_minutes % 60)
        . " mins";


    // // ======================================================
    // // STAFF LOAN / ADVANCE DEDUCTION
    // // ======================================================

    // $loan_due = 0;

    // $loanQry = $pdo->query("
    //     SELECT
    //         COALESCE(SUM(due_amount), 0) AS loan_due

    //     FROM staff_loan

    //     WHERE staff_id = '$staff_profile_id'

    //     AND DATE(due_start_date) <= '$month_end'
    //     AND DATE(due_end_date) >= '$month_start'
    // ");

    // $loanData = $loanQry->fetch();

    // if ($loanData) {
    //     $loan_due = (float)$loanData['loan_due'];
    // }


    // // ======================================================
    // // SALARY ADVANCE
    // // ======================================================

    // $salary_advance = 0;

    // $advanceQry = $pdo->query("
    //     SELECT
    //         COALESCE(SUM(advance_amount), 0) AS salary_advance

    //     FROM staff_salary_adavance

    //     WHERE company_id = '$company_id'
    //     AND staff_id = '$staff_profile_id'

    //     AND DATE(dedection_month) >= '$month_start'
    //     AND DATE(dedection_month) <= '$month_end'
    // ");

    // $advanceData = $advanceQry->fetch();

    // if ($advanceData) {
    //     $salary_advance = (float)$advanceData['salary_advance'];
    // }

    
    // ======================================================
    // STAFF LOAN / SALARY ADVANCE DEDUCTION
    // staff_loan.type = 1 : Staff Loan
    // staff_loan.type = 2 : Salary Advance
    // ======================================================

    $loan_due = 0;
    $salary_advance = 0;

    // First day of the selected payroll month
    $deduction_month = date('Y-m-01', strtotime($month_start));

    $loanQry = $pdo->prepare("
        SELECT
            sl.id,
            sl.type,
            sl.loan_amount,
            sl.total_intrest,
            sl.due_amount,
            sl.due_start_date,
            sl.due_end_date,

            COALESCE(
                (
                    SELECT SUM(sd.deduction_amount)
                    FROM staff_deduction sd
                    WHERE sd.staff_loan_id = sl.id
                      AND sd.staff_id = sl.staff_id
                      AND sd.deduction_month < ?
                ),
                0
            ) AS previous_deductions,

            (
                SELECT sd.deduction_amount
                FROM staff_deduction sd
                WHERE sd.staff_loan_id = sl.id
                  AND sd.staff_id = sl.staff_id
                  AND sd.deduction_month = ?
                ORDER BY sd.id DESC
                LIMIT 1
            ) AS current_deduction

        FROM staff_loan sl

        WHERE sl.company_id = ?
          AND sl.staff_id = ?
          AND DATE(sl.due_start_date) <= ?
          AND DATE(sl.due_end_date) >= ?
    ");

    $loanQry->execute([
        $deduction_month,
        $deduction_month,
        $company_id,
        $staff_profile_id,
        $month_end,
        $month_start
    ]);

    while ($loan = $loanQry->fetch(PDO::FETCH_ASSOC)) {

        $type = (int)$loan['type'];

        $loan_amount = (float)$loan['loan_amount'];
        $total_interest = (float)$loan['total_intrest'];
        $due_amount = (float)$loan['due_amount'];

        $previous_deductions =
            (float)$loan['previous_deductions'];

        // Total recoverable amount for this loan/advance
        $maximum_recoverable = $loan_amount + $total_interest;

        // Remaining amount before this month's deduction
        $remaining_balance = max(
            0,
            $maximum_recoverable - $previous_deductions
        );

        // Prefer the deduction record for the selected month.
        // If no record exists, use the configured due amount.
        if ($loan['current_deduction'] !== null) {
            $deduction_amount =
                (float)$loan['current_deduction'];
        } else {
            $deduction_amount = $due_amount;
        }

        // Prevent deductions exceeding the remaining balance
        $deduction_amount = min(
            max(0, $deduction_amount),
            $remaining_balance
        );

        if ($type === 1) {

            // Staff Loan
            $loan_due += $deduction_amount;

        } elseif ($type === 2) {

            // Salary Advance
            $salary_advance += $deduction_amount;
        }
    }

    $loan_due = round($loan_due, 2);
    $salary_advance = round($salary_advance, 2);


    // ======================================================
    // OT AMOUNT
    // ======================================================

    $ot_amount = 0;

    // CTC BASED OT
    if (
        $staff['ot_payment'] == 1
        &&
        $shift_hours > 0
        &&
        $working_days > 0
    ) {

        $per_hour =
            $total_ctc /
            $total_days /
            $shift_hours;

        $ot_amount =
            $per_hour * $total_ot_hours;
    }

    // FIXED OT
    elseif (
        $staff['ot_payment'] == 2
        &&
        $shift_hours > 0
    ) {

        $ot_amount =
            $staff['ot_per_day'] * $ot_days;
    }


    // ======================================================
    // SALARY COMPONENTS
    // ======================================================

    $getSalary = $pdo->query("
        SELECT

            sci.ctc_id,
            sci.ctc_amount,

            cc.salary_component,
            cc.component_category,
            cc.pay_frequency

        FROM staff_ctc_info sci

        LEFT JOIN ctc_creation cc
            ON cc.id = sci.ctc_id

        INNER JOIN (

            SELECT
                MAX(id) AS last_id

            FROM staff_ctc_info

            WHERE staff_profile_id = '$staff_profile_id'
              AND effective_from <= '$month_end'

            GROUP BY
                ctc_id,
                staff_profile_id

        ) latest

            ON latest.last_id = sci.id

        WHERE

            sci.effective_from <= '$month_end'

            AND (

                (
                    cc.status = 0
                    AND cc.effective_from <= '$month_start'
                )

                OR

                (
                    cc.status = 1
                    AND cc.effective_from > '$month_start'
                )

            )

            $stff_ctc_con
    ");


    while ($salary = $getSalary->fetch()) {

        $component_id = $salary['ctc_id'];
        $name = $salary['salary_component'];

        $amount = floatval(
            str_replace(',', '', $salary['ctc_amount'])
        );

        $component_category = $salary['component_category'];
        $pay_frequency = $salary['pay_frequency'];


        /*
        component_category:
        1 = Salary
        2 = Reimbursement

        pay_frequency:
        1 = Monthly
        2 = Per Day
        */


        // ==================================================
        // SALARY COMPONENT
        // ==================================================

        if ($component_category == 1) {

            // Salary components use payable days.
            // Extra working amount is added separately.

            if ($working_days > 0) {
                $amount =
                    ($amount / $working_days) *
                    $total_payable_days;
            }
        }


        // ==================================================
        // REIMBURSEMENT
        // ==================================================

        elseif ($component_category == 2) {

            // Per-day reimbursement
            if ($pay_frequency == 2) {
                $amount = $amount * $total_payable_days;
            }

            // Monthly reimbursement
            else {
                $amount = $amount;
            }
        }


        // ==================================================
        // PT ELIGIBLE EARNINGS
        // ==================================================

        if ($component_category == 1) {

            $pt_component_name = strtolower(trim($name));

            $is_bonus = strpos($pt_component_name, 'bonus') !== false;
            $is_gratuity = strpos($pt_component_name, 'gratuity') !== false;

            if (!$is_bonus && !$is_gratuity) {
                $pt_eligible_earnings += $amount;
            }
        }

        // ==================================================
        // PF WAGES: EARNED BASIC + DA ONLY
        // ==================================================

        if ($component_category == 1) {

            $normalized_component_name = strtolower(trim($name));

            if (in_array($normalized_component_name, [
                'basic',
                'basic salary',
                'basic pay',
                'da',
                'dearness allowance',
                'dearness allowance (da)'
            ], true)) {

                $pf_wages += $amount;
            }
        }


        // ==================================================
        // STORE COMPONENT AND ADD TO GROSS
        // ==================================================

        $components[$component_id] = round($amount, 2);

        $gross_total += $amount;
    }


    // ======================================================
    // GROSS SALARY BEFORE EMPLOYER CONTRIBUTIONS
    // ======================================================

    $gross_total += $extra_working_amount;
    $gross_total += $ot_amount;

    // OT and extra-working earnings are also PT eligible.
    $pt_eligible_earnings += $extra_working_amount;
    $pt_eligible_earnings += $ot_amount;


    // Keep this amount as the original gross for statutory
    // calculations. Employer contributions are added later.
    $gross_before_employer = $gross_total;


    // ======================================================
    // STATUTORY
    // ======================================================

    $statQry = $pdo->query("
        SELECT *
        FROM statutory_compliance
        WHERE company_id = '$company_id'
        ORDER BY id DESC
        LIMIT 1
    ");

    $stat = $statQry->fetch() ?? [];


    $employee_pf =
    $employer_pf =
    $admin_charge =
    $pension = 0;

    $employee_esi =
    $employer_esi =
    $pt = 0;


    // ======================================================
    // PF
    // Calculate only on earned Basic + DA
    // ======================================================

    $pf_salary = 0;

    if (
        !empty($stat)
        &&
        $staff['pf_available'] == 1
        &&
        $stat['pf_applicable'] == 1
    ) {

        // PF wages = earned Basic + earned DA only
        $pf_salary = $pf_wages;

        // Apply configured PF wage limit when enabled
        if (
            $stat['apply_pf_wage_limit'] == 1
            &&
            $pf_salary > (float)$stat['pf_wage_limit']
        ) {
            $pf_salary = (float)$stat['pf_wage_limit'];
        }

        // Employee PF contribution
        $employee_pf = (
            $pf_salary *
            (float)$stat['employee_contribution']
        ) / 100;

        // Employer PF contribution
        $employer_pf = (
            $pf_salary *
            (float)$stat['employer_contribution']
        ) / 100;

        // PF admin charge
        $admin_charge = (
            $pf_salary *
            (float)$stat['admin_charge']
        ) / 100;

        // Pension contribution
        $pension = (
            $pf_salary *
            (float)$stat['pension']
        ) / 100;
    }


    // ======================================================
    // ESI
    // ======================================================

    if (
        !empty($stat)
        &&
        $staff['esi_available'] == 1
        &&
        $stat['esi_applicable'] == 1
    ) {

        $employee_esi = (
            $gross_before_employer *
            $stat['employee_share']
        ) / 100;

        $employer_esi = (
            $gross_before_employer *
            $stat['employer_share']
        ) / 100;
    }


    // ======================================================
    // PROFESSIONAL TAX
    // ======================================================

    if (
        !$pt_history_mode
        &&
        !empty($stat)
        &&
        $staff['pt_available'] == 1
        &&
        $stat['professional_tax_applicable'] == 1
    ) {

        /*
         * PT is deducted only once per half-year:
         *
         * September -> April to September
         * March     -> October to March
         *
         * The slab is applied to the accumulated actual
         * eligible earnings of those six months.
         */

        $payroll_month_no = (int)date('n', strtotime($month_start));

        $pt = 0;
        $pt_half_year_earnings = 0;

        if ($payroll_month_no == 9 || $payroll_month_no == 3) {

            // Current month is already calculated above.
            $pt_half_year_earnings = $pt_eligible_earnings;

            // Five previous months.
            for ($i = 1; $i <= 5; $i++) {

                $history_date = new DateTime($month_start);
                $history_date->modify("-{$i} months");

                $history_month = $history_date->format('Y-m');

                $pt_half_year_earnings += getHistoricalPtEarnings(
                    $company_id,
                    $branch_id,
                    $dept_id,
                    $history_month,
                    $staff_profile_id
                );
            }

            /*
             * Use the configured slab only when calculation_type
             * is slab based and a valid slab matches.
             */
            $slabMatched = false;

            if (
                isset($stat['calculation_type'])
                &&
                $stat['calculation_type'] == 2
                &&
                !empty($stat['slab'])
            ) {

                $slabs = explode(",", $stat['slab']);

                foreach ($slabs as $slab) {

                    $parts = explode("=", trim($slab), 2);

                    if (count($parts) != 2) {
                        continue;
                    }

                    $range = explode("-", trim($parts[0]), 2);

                    if (
                        count($range) == 2
                        &&
                        is_numeric(trim($range[0]))
                        &&
                        is_numeric(trim($range[1]))
                    ) {

                        $minimum = (float)trim($range[0]);
                        $maximum = (float)trim($range[1]);
                        $slabAmount = (float)trim($parts[1]);

                        if (
                            $pt_half_year_earnings >= $minimum
                            &&
                            $pt_half_year_earnings <= $maximum
                        ) {
                            $pt = $slabAmount;
                            $slabMatched = true;
                            break;
                        }
                    }
                }
            }

            /*
             * Confirmed Tamil Nadu half-year slab.
             *
             * This fallback also handles the "above 75000" range,
             * which cannot be represented by the existing min-max
             * parser.
             */
            if (!$slabMatched) {

                if ($pt_half_year_earnings <= 21000) {
                    $pt = 0;
                } elseif ($pt_half_year_earnings <= 30000) {
                    $pt = 180;
                } elseif ($pt_half_year_earnings <= 45000) {
                    $pt = 425;
                } elseif ($pt_half_year_earnings <= 60000) {
                    $pt = 930;
                } elseif ($pt_half_year_earnings <= 75000) {
                    $pt = 1025;
                } else {
                    $pt = 1250;
                }
            }

        } else {

            // PT is not deducted in other payroll months.
            $pt = 0;
        }
    }

    // Historical PT mode never calculates/deducts PT.
    // It only exposes the monthly eligible earnings to the
    // September/March calculation above.
    if ($pt_history_mode) {
        $pt = 0;
    }

    // ======================================================
    // STAFF TYPE
    // ======================================================

    if ($staff['staff_type'] == 1) {

        $pf_amount = $employer_pf;
        $esi_amount = $employer_esi;

    } else {

        $pf_amount = $employee_pf;
        $esi_amount = $employee_esi;
    }


    // ======================================================
    // EMPLOYER CONTRIBUTIONS IN EARNINGS
    // ======================================================

    $employer_contribution_total =
        $employer_pf + $employer_esi;

    $gross_salary_with_employer =
        $gross_before_employer + $employer_contribution_total;


    // ======================================================
    // TOTAL DEDUCTION
    // Includes employer contributions as requested
    // ======================================================

    $deduction_total =
        $pf_amount
        +
        $admin_charge
        +
        $pension
        +
        $esi_amount
        +
        $pt
        +
        $total_attendance_deduction
        +
        $loan_due
        +
        $salary_advance
        +
        $employer_contribution_total;

    $net_salary_deduction =
        $pf_amount
        +
        $admin_charge
        +
        $pension
        +
        $esi_amount
        +
        $pt
        +
        $employer_contribution_total;

    


    // ======================================================
    // NET / TAKE HOME
    // ======================================================

    $Take_home =
        $gross_salary_with_employer - $deduction_total;

    $net_salary =$gross_salary_with_employer - $net_salary_deduction;


    // ======================================================
    // RESULT
    // ======================================================

    $result[] = array(

        'sno' => $sno,

        'staff_profile_id' => $staff_profile_id,
        'staff_id' => $staff['staff_id'],
        'staff_name' => $staff['staff_name'],
        'company_name' => $staff['company_name'],
        'department' => $staff['department'],
        'designation' => $staff['designation'],
        'team' => $staff['team'],

        // CTC
        'total_ctc' => number_format($total_ctc, 2),

        // ONE DAY SALARY
        'one_day_salary' => number_format($one_day_salary, 2),

        // DAYS
        'total_days' => $total_days,
        'weekoff' => $weekoff,
        'holidays' => $total_holidays,
        'working_days' => $working_days,
        'present_days' => $present_days,
        'approved_leave' => $approved_leave,

        // LOP
        'lop_days' => round($lop_days, 2),
        'lop_amount' => number_format($lop_amount, 2),

        // EARNED
        'earned_for_month' => number_format($earned_for_month, 2),

        // Monthly amount used for half-year PT accumulation.
        'pt_eligible_earnings' => number_format($pt_eligible_earnings, 2),

        // COMPONENTS
        'components' => $components,

        // EXTRA WORKING
        'extra_working' => round($extra_working_days, 2),
        'extra_working_amount' => number_format($extra_working_amount, 2),

        // OT
        'ot_hours' => $ot_hours_text,
        'ot_days' => round($ot_days, 2),
        'ot_amount' => number_format($ot_amount, 2),

        // EMPLOYER CONTRIBUTIONS IN EARNINGS
        'employer_pf_earning' => number_format($employer_pf, 2),
        'employer_esi_earning' => number_format($employer_esi, 2),

        // GROSS INCLUDING EMPLOYER CONTRIBUTIONS
        'gross_total' => number_format($gross_salary_with_employer, 2),

        // DEDUCTIONS
        'pf' => number_format($pf_amount, 2),
        'admin_charge' => number_format($admin_charge, 2),
        'pension' => number_format($pension, 2),
        'esi' => number_format($esi_amount, 2),
        'pt' => number_format($pt, 2),

        // OTHER DEDUCTIONS
        'loan_due' => number_format($loan_due, 2),
        'salary_advance' => number_format($salary_advance, 2),
        'attendance_deduction' => number_format($total_attendance_deduction, 2),

        // EMPLOYER CONTRIBUTIONS SHOWN AFTER DEDUCTIONS
        'employer_pf_deduction' => number_format($employer_pf, 2),
        'employer_esi_deduction' => number_format($employer_esi, 2),

        // TOTAL
        'deduction_total' => number_format($deduction_total, 2),

        // Net Salary
        'net_salary' => number_format($net_salary, 2),

        // TAKE HOME
        'take_home' => number_format($Take_home, 2)
    );

    $sno++;
}


// ==========================================================
// FINAL JSON
// ==========================================================

if ($pt_history_mode) {

    echo json_encode([
        'data' => $result
    ]);

    return;
}

echo json_encode([
    'components' => $componentArr,
    'data'       => $result
]);
