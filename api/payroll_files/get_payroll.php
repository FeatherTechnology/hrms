<?php

// Generate monthly payroll report based on company, branch, and selected month.
// Calculates attendance, leave, LOP, OT, salary components, statutory deductions, and net salary for each staff.

include '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$branch_id  = $_POST['branch_id'];
$month      = $_POST['month'];
$stff_con = '';
// in pay slip we use this condition to get the seperate pay slip 
if (isset($_POST['stf_prf_id']) && $_POST['stf_prf_id'] != '') {
    $stf_prf_id = $_POST['stf_prf_id'];
    $stff_con = "AND o.staff_profile_id = '$stf_prf_id'";
}
$result = array();

// GET ALL SALARY COMPONENT
$componentArr = array();

$getComponents = $pdo->query("
    SELECT id, salary_component
    FROM ctc_creation
    WHERE company_id = '$company_id'
");

while ($row = $getComponents->fetch()) {
    $componentArr[$row['id']] = $row['salary_component'];
}

// GET STAFF LIS
$getStaff = $pdo->query("
    SELECT 
        sc.id AS staff_profile_id,
        sc.staff_id,
        sc.staff_name,
        sc.staff_type,

        bc.branch_name,
        depcr.department_name as department,
        descr.designation as designation, 
        tc.team_name as team,
        cc.company_name,

        o.total_ctc,
        o.pf_available,
        o.esi_available,
        o.pt_available,
        o.shift,
        o.ot_payment,
        o.ot_per_day

    FROM staff_creation sc

    LEFT JOIN occupation_info o
        ON o.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = sc.id
        )

    LEFT JOIN company_creation cc ON cc.id = o.company_id
    LEFT JOIN branch_creation bc ON bc.id = o.branch_id
    LEFT JOIN department_creation depcr ON depcr.id = o.department
    LEFT JOIN designation_creation descr ON descr.id = o.designation
    LEFT JOIN team_name_creation tc ON tc.id = o.team

    WHERE o.company_id = '$company_id'
    AND o.branch_id = '$branch_id' $stff_con
");

$sno = 1;

while ($staff = $getStaff->fetch()) {

    $staff_profile_id = $staff['staff_profile_id'];

    $components = array();
    $gross_total = 0;

    // MONTH CALCULATION
    $start_date = $month . "-01";
    $end_date   = date("Y-m-t", strtotime($start_date));
    $total_days = date("t", strtotime($start_date));

    // WEEKOFF
    $weekoffQry = $pdo->query("
        SELECT cw.week_off 
        FROM company_weekoffs cw 
        JOIN company_policies cp 
            ON cp.id = cw.company_policies_id  
        WHERE cp.company_id = '$company_id'
    ");

    $weekoff = $weekoffQry->fetch()['week_off'] ?? 0;

    // HOLIDAYS
    $holidayQry = $pdo->query("
    SELECT IFNULL(SUM(no_of_days),0) as total_holidays
    FROM holiday_creation
    WHERE company_id = '$company_id'
    AND status = 0
    AND (
        from_date BETWEEN '$start_date' AND '$end_date'
        OR to_date BETWEEN '$start_date' AND '$end_date'
    )
");
    $total_holidays = $holidayQry->fetch()['total_holidays'] ?? 0;

    // WORKING DAYS
    $working_days = $total_days - $weekoff - $total_holidays;
    // SHIFT DETAILS
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

    // PRESENT DAYS
    $attQry = $pdo->query("
        SELECT COUNT(DISTINCT DATE(entry_time)) as present_days
        FROM attendance
        WHERE staff_profile_id = '$staff_profile_id'
        AND DATE(entry_time) BETWEEN '$start_date' AND '$end_date'
    ");

    $present_days = $attQry->fetch()['present_days'] ?? 0;

    // APPROVED LEAVE (req_type = 1) 
    $leaveQry = $pdo->query("
        SELECT approved_from_date, approved_to_date
        FROM regularization
        WHERE staff_profile_id = '$staff_profile_id'
        AND req_type = 1
        AND status = 1
        AND DATE(approved_from_date) <= '$end_date'
        AND DATE(approved_to_date) >= '$start_date'
    ");

    $approved_leave = 0;

    while ($leave = $leaveQry->fetch()) {

        $s = strtotime($leave['approved_from_date']);
        $e = strtotime($leave['approved_to_date']);

        $ms = strtotime($start_date);
        $me = strtotime($end_date);

        $a = max($s, $ms);
        $b = min($e, $me);

        if ($b >= $a) {
            $approved_leave += (($b - $a) / 86400) + 1;
        }
    }
    // TOTAL PAYABLE DAYS
    $total_payable_days = $present_days + $approved_leave;

    // EXTRA WORKING DAYS
    $extra_working_days = 0;
    if ($total_payable_days > $working_days) {
        $extra_working_days = $total_payable_days - $working_days;
    }

    // LOP DAYS
    $lop_days = 0;
    if ($total_payable_days < $working_days) {
        $lop_days = $working_days - $total_payable_days;
    }

    // OT (req_type = 4)
    $otQry = $pdo->query("
        SELECT approved_from_date, approved_to_date
        FROM regularization
        WHERE staff_profile_id = '$staff_profile_id'
        AND req_type = 4
        AND status = 1
        AND DATE(approved_from_date) <= '$end_date'
        AND DATE(approved_to_date) >= '$start_date'
    ");

    $total_ot_minutes = 0;

    while ($ot = $otQry->fetch()) {

        $ot_start = strtotime($ot['approved_from_date']);
        $ot_end   = strtotime($ot['approved_to_date']);

        if ($ot_end > $ot_start) {
            $total_ot_minutes += ($ot_end - $ot_start) / 60;
        }
    }

    $total_ot_hours = $total_ot_minutes / 60;

    $ot_days = ($shift_hours > 0) ? ($total_ot_hours / $shift_hours) : 0;

    $ot_hours_text = floor($total_ot_hours) . " hrs " . ($total_ot_minutes % 60) . " mins";

    // OT AMOUNT
    $ot_amount = 0;

    // CTC BASED OT
    if ($staff['ot_payment'] == 1 && $shift_hours > 0 && $working_days > 0) {

        $per_hour = $staff['total_ctc'] / $working_days / $shift_hours;

        $ot_amount = $per_hour * $total_ot_hours;
    }

    // FIXED OT
    else if ($staff['ot_payment'] == 2 && $shift_hours > 0) {

        $ot_amount = $staff['ot_per_day'] * $ot_days;
    }

    // SALARY COMPONENTS
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
        SELECT MAX(id) AS last_id
        FROM staff_ctc_info
        WHERE staff_profile_id = '$staff_profile_id'
        GROUP BY ctc_id, staff_profile_id
    ) latest 
        ON latest.last_id = sci.id
");

    while ($salary = $getSalary->fetch()) {

        $name = $salary['salary_component'];
        $amount = floatval(str_replace(',', '', $salary['ctc_amount']));

        $component_category = $salary['component_category'];
        $pay_frequency      = $salary['pay_frequency'];

        /* component_category 1 = Salary 2 = Reimbursement
        pay_frequency 1 = Monthly  2 = Per Day*/

        // SALARY COMPONENTS
        if ($component_category == 1) {

            // BASIC, DA, HRA, etc.
            if ($working_days > 0) {
                $amount = ($amount / $working_days) * $total_payable_days;
            }
        }
        // REIMBURSEMENT COMPONENTS
        else if ($component_category == 2) {

            // PER DAY REIMBURSEMENT
            if ($pay_frequency == 2) {
                $amount = $amount * $total_payable_days;
            }
            else {
                $amount = $amount;
            }
        }

        $components[$name] = round($amount, 2);

        $gross_total += $amount;
    }

    // ADD OT INTO GROSS
    $gross_total += $ot_amount;

    // STATUTORY
    $statQry = $pdo->query("
        SELECT * FROM statutory_compliance
        WHERE company_id = '$company_id'
        ORDER BY id DESC LIMIT 1
    ");

    $stat = $statQry->fetch() ?? [];

    $employee_pf = $employer_pf = $admin_charge = $pension = 0;
    $employee_esi = $employer_esi = $pt = 0;

    // PF
    if (!empty($stat) && $staff['pf_available'] == 1 && $stat['pf_applicable'] == 1) {

        $pf_salary = $gross_total;

        if ($stat['apply_wage_limit'] == 1 && $pf_salary > $stat['pf_wage_limit']) {
            $pf_salary = $stat['pf_wage_limit'];
        }

        $employee_pf = ($pf_salary * $stat['employee_contribution']) / 100;
        $employer_pf = ($pf_salary * $stat['employer_contribution']) / 100;
        $admin_charge = ($pf_salary * $stat['admin_charge']) / 100;
        $pension = ($pf_salary * $stat['pension']) / 100;
    }

    // ESI
    if (!empty($stat) && $staff['esi_available'] == 1 && $stat['esi_applicable'] == 1) {

        $employee_esi = ($gross_total * $stat['employee_share']) / 100;
        $employer_esi = ($gross_total * $stat['employer_share']) / 100;
    }

    // PT
    if (!empty($stat) && $staff['pt_available'] == 1 && $stat['professional_tax_applicable'] == 1) {

        if ($stat['calculation_type'] == 1) {
            $pt = (float)$gross_total * (float)$stat['percentage'] / 100;
        } else {
            $slabs = explode(",", $stat['slab']);
            foreach ($slabs as $slab) {
                $parts = explode("=", $slab);
                if (count($parts) == 2) {
                    $range = explode("-", $parts[0]);
                    if ($gross_total >= $range[0] && $gross_total <= $range[1]) {
                        $pt = $parts[1];
                        break;
                    }
                }
            }
        }
    }

    // STAFF TYPE
    if ($staff['staff_type'] == 1) {
        $pf_amount = $employer_pf;
        $esi_amount = $employer_esi;
    } else {
        $pf_amount = $employee_pf;
        $esi_amount = $employee_esi;
    }

    $deduction_total = $pf_amount + $admin_charge + $pension + $esi_amount + $pt;

    $net_salary = $gross_total - $deduction_total;

    // RESULT
    $result[] = array(
        'sno' => $sno,
        'staff_id' => $staff['staff_id'],
        'staff_name' => $staff['staff_name'],
        'company_name' => $staff['company_name'],
        'department' => $staff['department'],
        'designation' => $staff['designation'],
        'team' => $staff['team'],
        'total_ctc' => $staff['total_ctc'],

        'total_days' => $total_days,
        'weekoff' => $weekoff,
        'working_days' => $working_days,
        'present_days' => $present_days,
        'approved_leave' => $approved_leave,
        'lop_days' => $lop_days,

        'components' => $components,

        'gross_total' => number_format($gross_total, 2),
        'pf' => number_format($pf_amount, 2),
        'admin_charge' => number_format($admin_charge, 2),
        'pension' => number_format($pension, 2),
        'esi' => number_format($esi_amount, 2),
        'pt' => number_format($pt, 2),
        'deduction_total' => number_format($deduction_total, 2),
        'net_salary' => number_format($net_salary, 2),

        'extra_working' => $extra_working_days,

        'ot_hours' => $ot_hours_text,
        'ot_days' => round($ot_days, 2),
        'ot_amount' => number_format($ot_amount, 2),

    );

    $sno++;
}

echo json_encode([
    'components' => array_values($componentArr),
    'data' => $result
]);
