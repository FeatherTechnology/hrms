
<?php
require "../../ajaxconfig.php";
@session_start();

$user_id = $_SESSION['user_id'] ?? '';

/* Get Filters */
$company_id = $_POST['params']['company_id'] ?? '';
$branch_id = $_POST['params']['branch_id'] ?? '';
$department_id = $_POST['params']['department_id'] ?? '';
$status = $_POST['params']['status'] ?? '';
$screen = $_POST['params']['screen'] ?? '';

$today = date('Y-m-d');

/* Validate Filters */
if ($company_id === '' || ($branch_id === '' && $department_id === '')) {
    echo json_encode([
        "draw" => intval($_POST['draw']),
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
    exit;
}

/* DataTable Columns */
$column = [
    'sc.id',
    'sc.staff_id',
    'sc.staff_name',
    'sc.place',
    'cc.company_name',
    'bc.branch_name',
    'd.department_name',
    'ti.team_name',
    'des.designation',
    'sc.mobile1',
    'sc.id',
    'sc.id'
];

/* Main Query */
$query = "SELECT
            sc.id,
            sc.staff_id,
            sc.staff_name,
            sc.place,
            sc.staff_type,
            cc.company_name,
            bc.branch_name,
            d.department_name,
            des.designation,
            ti.team_name,
            sc.mobile1,
            sc.relieve_date,
            sc.status AS staff_status,

            CASE
                WHEN NOT EXISTS (
                    SELECT 1
                    FROM document_info di
                    WHERE di.staff_profile_id = sc.id
                )
                THEN 'No Document'

                WHEN NOT EXISTS (
                    SELECT 1
                    FROM document_info di
                    WHERE di.staff_profile_id = sc.id
                      AND di.return_date IS NULL
                )
                THEN 'Returned'

                ELSE 'Received'
            END AS document_status

        FROM staff_creation sc

        LEFT JOIN company_creation cc
            ON sc.company_id = cc.id

        INNER JOIN (
            SELECT *
            FROM occupation_info oi1
            WHERE oi1.id = (
                SELECT MAX(oi2.id)
                FROM occupation_info oi2
                WHERE oi2.staff_profile_id = oi1.staff_profile_id
                  AND oi2.effective_from <= NOW()
            )
        ) oc ON oc.staff_profile_id = sc.id

        LEFT JOIN branch_creation bc
            ON oc.branch_id = bc.id

        LEFT JOIN department_creation d
            ON oc.department = d.id

        LEFT JOIN team_name_creation ti
            ON oc.team = ti.id

        LEFT JOIN designation_creation des
            ON oc.designation = des.id

        WHERE 1=1 ";

/* Status Filter */
if ($status != '') {

    if ($status == 1) {
        $query .= " AND sc.status IN (1,2,3)";
    } elseif ($status == 2) {
        $query .= " AND sc.status = 4";
    }

} else {
    $query .= " AND sc.status IN (2,3)";
}

/* Company Filter */
if ($company_id != '') {
    $query .= " AND sc.company_id = '$company_id'";
}

/* Branch Filter */
if ($branch_id != '') {
    $query .= " AND oc.branch_id = '$branch_id'";
}

/* Department Filter */
if ($department_id != '') {
    $query .= " AND oc.department = '$department_id'";
}

/* Search */
if (!empty($_POST['search'])) {

    $search = $_POST['search'];

    $query .= " AND (
        sc.staff_id LIKE '$search%'
        OR sc.staff_name LIKE '%$search%'
        OR sc.place LIKE '%$search%'
        OR sc.staff_type LIKE '%$search%'
        OR sc.mobile1 LIKE '%$search%'
        OR cc.company_name LIKE '%$search%'
        OR bc.branch_name LIKE '%$search%'
        OR d.department_name LIKE '%$search%'
        OR ti.team_name LIKE '%$search%'
        OR des.designation LIKE '%$search%'
    )";
}

/* Ordering */
if (isset($_POST['order'])) {

    $order_column = intval($_POST['order'][0]['column']);
    $order_dir = strtoupper($_POST['order'][0]['dir']);

    if (isset($column[$order_column])) {
        $query .= " ORDER BY " . $column[$order_column] .
                  ($order_dir === 'ASC' ? ' ASC' : ' DESC');
    }

} else {
    $query .= " ORDER BY sc.id DESC";
}

/* Pagination */
$query1 = '';

if ($_POST['length'] != -1) {
    $start = max(0, intval($_POST['start']));
    $length = max(0, intval($_POST['length']));

    $query1 = " LIMIT $start, $length";
}

/* Execute Query */
$statement = $pdo->prepare($query);
$statement->execute();

$number_filter_row = $statement->rowCount();

/* Fetch Records */
$statement = $pdo->prepare($query . $query1);
$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

/* Prepare Data */
$data = [];
$sno = intval($_POST['start']) + 1;

foreach ($result as $row) {

    $sub_array = [];

    $sub_array[] = $sno++;
    $sub_array[] = $row['staff_id'];
    $sub_array[] = $row['staff_name'];
    $sub_array[] = $row['place'];
    $sub_array[] = $row['company_name'];
    $sub_array[] = $row['branch_name'];
    $sub_array[] = $row['department_name'];
    $sub_array[] = $row['team_name'];
    $sub_array[] = $row['designation'];
    $sub_array[] = $row['mobile1'];

    $exit_message = '';
    $btn_message = '';
    $status_messages = [];

    /* Document and Exit Status */
    if ($status == '') {

        $document_status = $row['document_status'];

        $document_color = in_array($document_status, ['Returned', 'No Document'], true)
            ? "background-color:#d4edda;color:#155724;"
            : "background-color:#FFFF00;color:#856404;";

        $sub_array[] = "<span style='$document_color padding:5px 10px;border-radius:5px;font-weight:500;'>$document_status</span>";

        $sub_array[] = !empty($row['relieve_date'])
            ? date('d-m-Y', strtotime($row['relieve_date']))
            : '';

        if (in_array((int)$row['staff_status'], [2, 3], true)) {

            if (
                in_array($document_status, ['Returned', 'No Document'], true) &&
                !empty($row['relieve_date']) &&
                date('Y-m-d', strtotime($row['relieve_date'])) < $today
            ) {

                $exit_message = "<button type='button' class='btn btn-outline-secondary staff_exit_sts' value='{$row['id']}' title='Move to Exit'><span class='icon-arrow_forward'></span></button>";
                $btn_message="Exit in Progress";

            } else {
                $exit_message = "Exit in Progress";
            }
        }

        $sub_array[] = $exit_message;

    } else {

        $status_messages = [
            '1' => "<button type='button' class='btn btn-outline-secondary staff_status' value='{$row['id']}' title='Move to Staff Exit'><span class='icon-arrow_forward'></span></button>",
            '2' => "Exit in Progress",
            '3' => "Exit in Progress",
            '4' => "Exited"
        ];

        $sub_array[] = $status_messages[$row['staff_status']] ?? '';
    }

    /* Check Exit in Progress */
    $exit_in_progress = (
        $exit_message === "Exit in Progress" || 
        (
            isset($status_messages[$row['staff_status']]) &&
            $status_messages[$row['staff_status']] === "Exit in Progress"
        )
    );

/* Staff Creation and Staff Exit Screens */
if (in_array($screen, ['staff_creation'], true)) {

    $is_exited = (int)$row['staff_status'] === 4;

    /* Suspension Button */
    if (
        ($screen == 'staff_creation' && $exit_in_progress) ||
        $is_exited
    ) {

        $title = $is_exited
            ? 'Cannot Suspend - Staff Already Exited'
            : 'Cannot Suspend - Exit in Progress';

        $sub_array[] = "<button type='button'
            class='btn btn-outline-secondary'
            value='{$row['id']}'
            disabled
            style='opacity:0.4;cursor:not-allowed;'
            title='$title'>
            Suspension
        </button>";

    } else {

        $sub_array[] = "<button type='button'
            class='btn btn-outline-secondary staff_suspend_btn'
            value='{$row['id']}'
            title='Suspend Staff'
            data-toggle='modal'
            data-target='#add_Suspension_modal'>
            Suspension
        </button>";
    }

    /* Edit Button */
    $edit_style = (
        $screen == 'staff_creation' && $exit_in_progress
    )
        ? "style='opacity:0.4;cursor:not-allowed;pointer-events:none;' title='Edit disabled - Exit in Progress'"
        : '';

    $sub_array[] = "<span class='icon-border_color staffEditBtn'
        value='{$row['id']}'
        $edit_style></span>";

} else {

    $sub_array[] = "<span class='icon-border_color staffEditBtn'
        value='{$row['id']}'></span>";
}

    $data[] = $sub_array;
}

/* Total Count */
function count_all_data($pdo)
{
    return $pdo->query("SELECT COUNT(*) FROM staff_creation")->fetchColumn();
}

/* Output */
$output = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => count_all_data($pdo),
    "recordsFiltered" => $number_filter_row,
    "data" => $data
];

echo json_encode($output);
