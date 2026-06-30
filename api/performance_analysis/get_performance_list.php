 <?php
    require '../../ajaxconfig.php';

    $company_id = $_POST['company_id'];
    $performance_list_arr = array();
    $i = 0;
    $qry = $pdo->query("SELECT * FROM performance_analysis WHERE company_id = '$company_id' and status = 0");

    if ($qry->rowCount() > 0) {

        while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
            $performance_list_arr[$i]['id'] = $row['id'];
            $performance_list_arr[$i]['criteria'] = !empty($row['criteria']) ? $row['criteria'] : '-';
            $performance_list_arr[$i]['target_perform'] = !empty($row['target_perform']) ? $row['target_perform'] : '-';
            $performance_list_arr[$i]['weightage'] = !empty($row['weightage']) ? $row['weightage'] : '-';
            $performance_list_arr[$i]['effective_from'] = !empty($row['effective_from']) ? date('F Y', strtotime($row['effective_from'] . '-01'))  : '-';
            $action_buttons = "<span class='icon-border_color performanceActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;";
            $action_buttons .= "<span class='icon-delete performanceDeleteBtn' value='" . $row['id'] . "'></span>";
            $performance_list_arr[$i]['action'] = $action_buttons;

            $i++;
        }
    }

    echo json_encode($performance_list_arr);
    $pdo = null; // Close Connection
    ?>