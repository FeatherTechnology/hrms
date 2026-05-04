<?php 
require "../../ajaxconfig.php";
$branch_id = $_POST['params']['branch_id'];

// $area_name_arr = array();
// $qry = $pdo->query("SELECT id,areaname,status FROM area_name_creation WHERE branch_id ='$branch_id' ");
// if($qry->rowCount()>0){
//     while($areaname_info = $qry->fetch(PDO::FETCH_ASSOC)){
//         $areaname_info['status'] = ($areaname_info['status'] =='1') ? 'Enable' : 'Disable';
//         $areaname_info['action'] = "<span class='icon-border_color areanameActionBtn' value='" . $areaname_info['id'] . "'></span>  <span class='icon-trash-2 areanameDeleteBtn' value='" . $areaname_info['id'] . "'></span>";
//         $area_name_arr[] = $areaname_info;
//     }
// }

// $pdo = null; //Connection Close.

// echo json_encode($area_name_arr);

$column = array(
    'id',
    "areaname",
    "status",
    'id'
);
$query = "SELECT id,areaname,status FROM area_name_creation WHERE branch_id ='$branch_id' ";
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND ( areaname LIKE '" . $search . "%')";
    }
}

if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}
$query1 = '';
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $query1 = ' LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}

$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();
$result = $statement->fetchAll();
$sno = isset($_POST['start']) ? $_POST['start'] + 1 : 1;
$data = [];
foreach ($result as $row) {
    $sub_array = array();
    $sub_array[] = $sno++;
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
   // $sub_array[] = isset($row['status']) ? $row['status'] : '';
  
    $sub_array[] = ($row['status'] =='1') ? 'Enable' : 'Disable';
    $action = "<span class='icon-border_color areanameActionBtn' value='" . $row['id'] . "'></span><span class='icon-trash-2 areanameDeleteBtn' value='" . $row['id'] . "'></span>";
    $sub_array[] = $action;

    $data[] = $sub_array;
}
function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM area_name_creation";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}

$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
?>

