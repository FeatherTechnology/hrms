<?php

require '../../ajaxconfig.php';

$company_id = $_POST['company_id'];
$screen = $_POST['screen'];
$dep_name = $_POST['dep_name'];

$cmpy_dep_per_arr = [];

if ( empty($screen)  ) {
    echo json_encode([]);
    exit;
}

$i = 0;

$sql = "SELECT
            cp.id,
            cp.permission_type,
            cp.max_permission,
            cp.department_id,
            dc.department_name,
            cp.effective_date
        FROM company_policies cp
        LEFT JOIN department_creation dc
            ON dc.id = cp.department_id
        WHERE cp.company_id = ?";

$params = [$company_id];

if (!empty($dep_name)) {
    $sql .= " AND cp.department_id = ?";
    $params[] = $dep_name;
}

// For outer screen, show only the policy effective in the current month
if ($screen == 'outer') {
    $sql .= " AND cp.id = (
        SELECT cp2.id
        FROM company_policies cp2
        WHERE cp2.company_id = cp.company_id
          AND cp2.department_id = cp.department_id
          AND cp2.effective_date <= CURDATE()
        ORDER BY cp2.effective_date DESC, cp2.id DESC
        LIMIT 1
    )";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if ($stmt->rowCount() > 0) {

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $cmpy_dep_per_arr[$i]['id'] = $row['id'];
        $cmpy_dep_per_arr[$i]['department_name'] = $row['department_name'];
        $cmpy_dep_per_arr[$i]['permission_type'] = ($row['permission_type'] == 1) ? 'Count' : (($row['permission_type'] == 2) ? 'Minute' : '');
        $cmpy_dep_per_arr[$i]['max_permission'] = $row['max_permission'];
        $cmpy_dep_per_arr[$i]['effective_date'] = !empty($row['effective_date'])  ? date('M - Y', strtotime($row['effective_date'])) : '';

        if ($screen == 'inner') {

            // Compare effective month with current month
            $is_editable = !empty($row['effective_date']) &&
                date('Y-m', strtotime($row['effective_date'])) >= date('Y-m');

            if ($is_editable) {

                // Current and future months: Enable Edit and Delete
                $cmpy_dep_per_arr[$i]['action'] = "
                    <span class='icon-border_color permissionInfoActionBtn'
                        value='" . $row['id'] . "'
                        data-permission_type='" . $row['permission_type'] . "'
                        data-max_permission='" . $row['max_permission'] . "'
                        data-effective_date='" . $row['effective_date'] . "'>
                    </span>
                    &nbsp;&nbsp;&nbsp;
                    <span class='icon-delete permissionInfoDeleteBtn'
                        value='" . $row['id'] . "'>
                    </span>
                ";

            } else {

                // Previous month: Disabled Edit and Delete styles
                $cmpy_dep_per_arr[$i]['action'] = "
                    <span class='icon-border_color text-muted'
                        style='opacity:0.4; cursor:not-allowed;'
                        title='Previous month policy cannot be edited'>
                    </span>
                    &nbsp;&nbsp;&nbsp;
                    <span class='icon-delete text-muted'
                        style='opacity:0.4; cursor:not-allowed;'
                        title='Previous month policy cannot be deleted'>
                    </span>
                ";
            }
        }

        $i++;
    }
}

$pdo = null;

echo json_encode($cmpy_dep_per_arr);