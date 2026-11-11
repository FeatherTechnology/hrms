
<?php
require '../../ajaxconfig.php';

$staff_id = $_POST['staff_id'] ?? '';

$suspension_list_arr = [];
$i = 0;

// Current month starting date
$current_month = date('Y-m-01');

// Get Staff Name
$staff_stmt = $pdo->prepare("
    SELECT staff_name 
    FROM staff_creation 
    WHERE id = :staff_id
");
$staff_stmt->execute([':staff_id' => $staff_id]);

$staff_name = $staff_stmt->fetchColumn();

// Get Suspension Details
$stmt = $pdo->prepare("
    SELECT ss.id, ss.staff_profile_id, ss.from_date, ss.to_date, ss.reason
    FROM staff_suspended ss
    WHERE ss.staff_profile_id = :staff_id
");

$stmt->execute([':staff_id' => $staff_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $suspension_list_arr[$i]['id'] = $row['id'];
    $suspension_list_arr[$i]['staff_name'] = $staff_name;
  $suspension_list_arr[$i]['from_date'] = date('d-m-Y', strtotime($row['from_date']));
$suspension_list_arr[$i]['to_date'] = date('d-m-Y', strtotime($row['to_date']));
    $suspension_list_arr[$i]['reason'] = $row['reason'];

    // Check whether suspension belongs to current or future month
    if ($row['from_date'] >= $current_month) {

        // Edit and Delete enabled
        $action_buttons = "<span class='icon-border_color suspension_edit' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete suspension_delete' value='" . $row['id'] . "'></span>";

    } else {

        // Edit and Delete disabled
        $action_buttons = "<span class='icon-border_color text-muted' style='opacity:0.4; cursor:not-allowed;' title='Previous month records cannot be edited'></span>&nbsp;&nbsp;&nbsp;";
        $action_buttons .= "<span class='icon-delete text-muted' style='opacity:0.4; cursor:not-allowed;' title='Previous month records cannot be deleted'></span>";
    }

    $suspension_list_arr[$i]['action'] = $action_buttons;

    $i++;
}

// Return Staff Name and Suspension Details
echo json_encode([
    'staff_name' => $staff_name,
    'suspension_list' => $suspension_list_arr
]);

$pdo = null;
?>
