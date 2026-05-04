<?php
require "../../ajaxconfig.php";

$user_arr = array();
$qry = $pdo->query("SELECT 
    u.id, 
    u.name, 
    u.user_name, 
    r.role, 
    d.designation, 
    GROUP_CONCAT(DISTINCT bc.branch_name ORDER BY bc.branch_name SEPARATOR ', ') AS branch_names,
    GROUP_CONCAT(DISTINCT lnc.linename ORDER BY lnc.linename SEPARATOR ', ') AS line_names
FROM 
    users u 
LEFT JOIN 
    branch_creation bc ON FIND_IN_SET(bc.id, u.branch)
LEFT JOIN 
    line_name_creation lnc ON FIND_IN_SET(lnc.id, u.line)
LEFT JOIN
	role r ON u.role = r.id
LEFT JOIN 
	designation d ON u.designation = d.id
GROUP BY 
    u.id");
if ($qry->rowCount() > 0) {
    while ($user_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        $user_info['action'] = "<span class='icon-border_color userActionBtn' value='" . $user_info['id'] . "'></span>  <span class='icon-trash-2 userDeleteBtn' value='" . $user_info['id'] . "'></span>";
        $user_arr[] = $user_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($user_arr);