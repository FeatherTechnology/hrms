<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$result = array();

/* ===============================
   STAFF + LAST OCCUPATION INFO
=================================*/
$qry = $pdo->query("
    SELECT 
        sc.*,

        oi.branch_id,
        oi.department,
        oi.team,
        oi.designation,
        oi.off_type,
        oi.branch_admin,
        oi.reporting_person,
        oi.branch,
        oi.pf_available,
        oi.esi_available,
        oi.pt_available,
        oi.total_ctc,
        oi.annual_ctc,
        oi.shift,
        oi.ot_payment,
        oi.ot_per_hour,
        oi.ot_per_day

    FROM staff_creation sc

    LEFT JOIN occupation_info oi 
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = sc.id
        )

    WHERE sc.id = '$id'
");

if ($qry->rowCount() > 0) {
    $result = $qry->fetch(PDO::FETCH_ASSOC);
}

/* ===============================
   LAST CTC INFO (same created_date batch)
=================================*/
$ctcQry = $pdo->query("
    SELECT *
    FROM staff_ctc_info
    WHERE staff_profile_id = '$id'
    AND created_date = (
        SELECT MAX(created_date)
        FROM staff_ctc_info
        WHERE staff_profile_id = '$id'
    )
");

$ctcData = array();

if ($ctcQry->rowCount() > 0) {
    $ctcData = $ctcQry->fetchAll(PDO::FETCH_ASSOC);
}

/* ===============================
   FINAL RESPONSE
=================================*/
echo json_encode([
    'staff' => $result,
    'ctc'   => $ctcData
]);

$pdo = null;
?>