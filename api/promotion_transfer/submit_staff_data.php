<?php
require '../../ajaxconfig.php';
@session_start();

$user_id = $_SESSION['user_id'];

/* POST VALUES */
$staff_profile_id = $_POST['staff_profile_id'];
$company_name     = $_POST['company_name'];
$effective_date = $_POST['effective_date']; // 2026-07

if (!empty($effective_date)) {
    $effective_date = date('Y-m-d', strtotime($effective_date . '-01'));
}
$branch_name      = $_POST['branch_name'];
$department       = $_POST['department'];
$team             = $_POST['team'];
$designation      = $_POST['designation'];
$reporting_person = $_POST['reporting_person'];
$reporting_person_type = (strtolower($_POST['reporting_person_type']) == 'director') ? 1 : 2;

$branch_admin     = $_POST['branch_admin'];
$branch           = $_POST['branch'];
$total_ctc        = $_POST['total_ctc'];
$annual_ctc       = $_POST['annual_ctc'];
$occ_status       = $_POST['occ_status'];
$total_amount = $_POST['total_ctc_amount'];

$staff_id = $_POST['staff_id'];
$staff_type = $_POST['staff_type'];

/* ===============================
   GET LAST OCCUPATION INFO
=================================*/
$getOcc = $pdo->query("
    SELECT *
    FROM occupation_info
    WHERE staff_profile_id = '$staff_profile_id'
    ORDER BY id DESC
    LIMIT 1
");

$oldOcc = $getOcc->fetch(PDO::FETCH_ASSOC);

/* ===============================
   DEFAULT OLD VALUES
=================================*/
$company_id_old     = $oldOcc['company_id'];
$branch_id_old      = $oldOcc['branch_id'];
$department_old     = $oldOcc['department'];
$team_old           = $oldOcc['team'];
$designation_old    = $oldOcc['designation'];
$off_type_old       = $oldOcc['off_type'];
$branch_admin_old   = $oldOcc['branch_admin'];
$reporting_old      = $oldOcc['reporting_person'];
$reporting_person_type_old     = $oldOcc['reporting_person_type'];
$branch_old         = $oldOcc['branch'];

$total_ctc_old      = $oldOcc['total_ctc'];
$annual_ctc_old     = $oldOcc['annual_ctc'];

$shift_old          = $oldOcc['shift'];
$ot_payment_old     = $oldOcc['ot_payment']; 
$ot_per_day_old     = $oldOcc['ot_per_day'];


/* ===============================
   PROMOTION
=================================*/
if ($occ_status == '1') {

    $designation_old = $designation;

    if ($staff_type != '1') {

        $reporting_old = $reporting_person;
        $reporting_person_type_old = $reporting_person_type;
    }
}


/* ===============================
   TRANSFER
=================================*/
if ($occ_status == '2') {

    $branch_id_old    = $branch_name;
    $department_old   = $department;
    $team_old         = $team;
    $branch_admin_old = $branch_admin;
    $branch_old       = $branch;
}


/* ===============================
   INCREMENT
=================================*/
if ($occ_status == '3') {

    $total_ctc_old  = $total_ctc;
    $annual_ctc_old = $annual_ctc;


    $ctcDetails = json_decode($_POST['ctcDetails'], true);
}


/* ===============================
   INSERT OCCUPATION INFO
=================================*/
$pdo->query("
    INSERT INTO occupation_info SET

    staff_profile_id = '$staff_profile_id',
    staff_id         = '$staff_id',

    company_id       = '$company_id_old',
    branch_id        = '$branch_id_old',
    department       = '$department_old',
    team             = '$team_old',
    designation      = '$designation_old',

    off_type         = '$off_type_old',

    branch_admin     = '$branch_admin_old',
    reporting_person = '$reporting_old',
    reporting_person_type = '$reporting_person_type_old',
    branch           = '$branch_old',

    total_ctc        = '$total_ctc_old',
    annual_ctc       = '$annual_ctc_old',

    shift            = '$shift_old',
    ot_payment       = '$ot_payment_old',
    ot_per_day       = '$ot_per_day_old',

    effective_from   = '$effective_date',
    occ_status       = '$occ_status',

    insert_login_id  = '$user_id',
    created_on       = NOW()
");


/* ===============================
   INSERT CTC INFO ONLY FOR INCREMENT
=================================*/
if ($occ_status == '3') {

    foreach ($ctcDetails as $row) {

        $ctc_id         = $row['ctc_id'];
        $ctc_amount     = $row['ctc_amount'];
        $ctc_percentage = $row['ctc_percentage'];

        $pdo->query("
            INSERT INTO staff_ctc_info SET

            staff_profile_id = '$staff_profile_id',
            staff_id         = '$staff_id',

            ctc_id           = '$ctc_id',
            ctc_amount       = '$ctc_amount',
            ctc_percentage   = '$ctc_percentage',

            total_ctc        = '$total_ctc',
            total_amount     = '$total_amount',

            insert_login_id  = '$user_id',
            created_date     = NOW()
        ");
    }
}

echo json_encode([
    'result' => 1
]);
?>