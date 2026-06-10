<?php
//  to get the attendance report based on the company , branch , department , staff is and selected month 

include '../../ajaxconfig.php';

$draw   = $_POST['draw'];
$start  = $_POST['start'];
$length = $_POST['length'];

$company_id = $_POST['company_id'] ?? '';
$branch_id  = $_POST['branch_id'] ?? '';
$department = $_POST['department'] ?? '';
$staff_id   = $_POST['staff_id'] ?? '';
$month      = $_POST['month'] ?? '';

$searchValue = $_POST['search']['value'] ?? '';

$daysInMonth = date('t', strtotime($month . '-01'));

/* ---------------- WHERE FILTER ---------------- */
$where = " WHERE sc.status = 1 ";

if (!empty($company_id)) {
    $where .= " AND oi.company_id = '$company_id' ";
}

if (!empty($branch_id)) {
    $where .= " AND oi.branch_id = '$branch_id' ";
}

if (!empty($department)) {
    $where .= " AND oi.department = '$department' ";
}

if (isset($staff_id) && $staff_id > 0) {
    $where .= " AND sc.id = '$staff_id' ";
}

/* ---------------- SEARCH ---------------- */
if (!empty($searchValue)) {
    $where .= " AND (sc.staff_name LIKE '%$searchValue%' ) ";
}

/* ---------------- TOTAL COUNT ---------------- */
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM staff_creation sc
    LEFT JOIN occupation_info oi 
        ON oi.id = (
            SELECT MAX(id) 
            FROM occupation_info 
            WHERE staff_profile_id = sc.id
        )
    $where
");
$totalStmt->execute();
$recordsTotal = $totalStmt->fetchColumn();

/* ---------------- STAFF LIST ---------------- */
$stmt = $pdo->prepare("
    SELECT 
    sc.id,
    sc.staff_name,
    sc.joining_date,
    sc.relieve_date
    FROM staff_creation sc
    LEFT JOIN occupation_info oi 
        ON oi.id = (
            SELECT MAX(id) 
            FROM occupation_info 
            WHERE staff_profile_id = sc.id
        )
    $where
    LIMIT :start, :length
");

$stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
$stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

$stmt->execute();
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------- ATTENDANCE DATA ---------------- */
$attStmt = $pdo->prepare("
    SELECT staff_profile_id, DATE(entry_time) dt
    FROM attendance
    WHERE DATE_FORMAT(entry_time,'%Y-%m') = ?
");

$attStmt->execute([$month]);

$att = [];
while ($r = $attStmt->fetch(PDO::FETCH_ASSOC)) {
    $att[$r['staff_profile_id']][] = $r['dt'];
}

/* ---------------- RESPONSE ---------------- */
$data = [];

/* ---------------- REGULARIZATION DATA ---------------- */
$regStmt = $pdo->prepare("
    SELECT
        staff_profile_id,
        req_type,
        approved_from_date,
        approved_to_date
    FROM regularization
    WHERE status = 1
      AND req_type IN (1,3)
      AND (
            DATE_FORMAT(approved_from_date,'%Y-%m') = ?
         OR DATE_FORMAT(approved_to_date,'%Y-%m') = ?
      )
");

$regStmt->execute([$month, $month]);

$regularization = [];

while ($r = $regStmt->fetch(PDO::FETCH_ASSOC)) {

    $from = strtotime($r['approved_from_date']);
    $to   = strtotime($r['approved_to_date']);

    while ($from <= $to) {

        $date = date('Y-m-d', $from);

        $regularization[$r['staff_profile_id']][$date]
            = $r['req_type'];

        $from = strtotime('+1 day', $from);
    }
}

foreach ($staffList as $s) {

    $joiningDate = !empty($s['joining_date'])
        ? date('Y-m-d', strtotime($s['joining_date']))
        : '';

    $relieveDate = !empty($s['relieve_date'])
        ? date('Y-m-d', strtotime($s['relieve_date']))
        : '';

    // Hide employee completely after relieve month
    if (!empty($relieveDate)) {

        $relieveMonth = date('Y-m', strtotime($relieveDate));

        if ($month > $relieveMonth) {
            continue;
        }
    }

    $row = [];
    $row['staff_name'] = $s['staff_name'];

    for ($i = 1; $i <= $daysInMonth; $i++) {

        $date = date(
            'Y-m-d',
            strtotime($month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT))
        );

        // Before joining date = Empty
        if (!empty($joiningDate) && $date < $joiningDate) {
            $row['d' . $i] = '';
            continue;
        }

        // On and after relieve date = Resigned
        if (!empty($relieveDate) && $date >= $relieveDate) {
            $row['d' . $i] =
                "<span class='badge bg-secondary'>R</span>";
            continue;
        }

        $today = date('Y-m-d');

        if (
            $date > $today &&
            date('Y-m', strtotime($date)) == date('Y-m')
        ) {

            $status = '';

        } else {

            if (isset($regularization[$s['id']][$date])) {

                $status = $regularization[$s['id']][$date];

            } else {

                $status = (
                    isset($att[$s['id']]) &&
                    in_array($date, $att[$s['id']])
                ) ? 'P' : 'A';
            }
        }

        switch ($status) {

            case 'P':
                $row['d' . $i] =
                    "<span class='badge bg-success'>P</span>";
                break;

            case 'A':
                $row['d' . $i] =
                    "<span class='badge bg-danger'>A</span>";
                break;

            case 1: // Leave
                $row['d' . $i] =
                    "<span class='badge bg-primary'>L</span>";
                break;

            case 3: // Week Off
                $row['d' . $i] =
                    "<span class='badge bg-info text-dark'>WO</span>";
                break;

            default:
                $row['d' . $i] = '';
        }
    }

    $data[] = $row;
}

/* ---------------- OUTPUT ---------------- */
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsTotal,
    "data" => $data
]);

$pdo = null;
