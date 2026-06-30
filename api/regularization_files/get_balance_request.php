<?php
// Get leave, permission, week-off, or OT balance for selected staff.

require "../../ajaxconfig.php";

$req_type     = $_POST['req_type'] ?? '';
$cmpy_id      = $_POST['cmpy_id'] ?? '';
$staff_id     = $_POST['staff_id'] ?? '';
$from_Date    = $_POST['from_date'] ?? '';
$to_date      = $_POST['to_date'] ?? '';
$leave_period = $_POST['leave_period'] ?? '';
$leave_type   = $_POST['leave_type'] ?? '';

$query = "";


/* ================= LEAVE ================= */

if ($req_type == '1') {

$query = "SELECT 
    lc.no_of_days,
    sc.start_time,
    sc.end_time,
    (
        COALESCE(
            SUM(
                CASE
                    WHEN reg.leave_period IN (1,2) THEN 0.5
                    ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                END
            ),0
        )
        +
        CASE
            WHEN :leave_period IN (1,2) THEN 0.5
            ELSE DATEDIFF(:to_date,:from_date) + 1
        END
    ) AS used_count,
    lc.no_of_days -
    ( COALESCE(
            SUM(
                CASE
                    WHEN reg.leave_period IN (1,2) THEN 0.5
                    ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                END
            ),0
        )
        +
        CASE
            WHEN :leave_period IN (1,2) THEN 0.5
            ELSE DATEDIFF(:to_date,:from_date) + 1
        END
    ) AS balance
    FROM leave_creation lc
    LEFT JOIN regularization reg
    ON reg.company_id = lc.company_id
    AND reg.staff_profile_id = :staff_id
    AND reg.leave_type = lc.id
    AND YEAR(reg.from_date) = YEAR(:from_date)
    AND reg.status IN (0,1)
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = :staff_id
        )
    INNER JOIN shift_creation sc 
        ON sc.id = oi.shift


    WHERE lc.company_id = :cmpy_id
    AND lc.id = :leave_type    ";

}


/* ================= PERMISSION ================= */

else if ($req_type == '2') {

$query = " SELECT 
    sc.start_time,
    sc.end_time,
    cp.max_permission,
    COUNT(reg.id) AS used_count,
    (cp.max_permission - COUNT(reg.id)) AS balance
    FROM company_policies cp
    LEFT JOIN regularization reg
        ON reg.company_id = cp.company_id
        AND reg.req_type = :req_type
        AND reg.staff_profile_id = :staff_id
        AND YEAR(reg.from_date) = YEAR(:from_date)
        AND MONTH(reg.from_date) = MONTH(:from_date)
        AND reg.status IN (0,1)
    LEFT JOIN occupation_info oi
        ON oi.id = (
            SELECT MAX(id)
            FROM occupation_info
            WHERE staff_profile_id = :staff_id
        )
    INNER JOIN shift_creation sc 
        ON sc.id = oi.shift
    WHERE cp.company_id = :cmpy_id
    GROUP BY cp.max_permission ";
}


/* ================= WEEK OFF ================= */

else if ($req_type == '3') {
    $query = "SELECT 
                SUM(cw.week_off) AS no_of_days,
                sc.start_time,
                sc.end_time,
                (COALESCE(
                        SUM(
                            CASE
                                WHEN reg.leave_period IN (1,2) THEN 0.5
                                ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                            END
                        ),0
                    )
                    +
                    CASE
                        WHEN :leave_period IN (1,2) THEN 0.5
                        ELSE DATEDIFF(:to_date, :from_date) + 1
                    END
                ) AS used_count,

                ( SUM(cw.week_off)
                    -
                    (
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN reg.leave_period IN (1,2) THEN 0.5
                                    ELSE DATEDIFF(reg.to_date, reg.from_date) + 1
                                END
                            ),0
                        )
                        +
                        CASE
                            WHEN :leave_period IN (1,2) THEN 0.5
                            ELSE DATEDIFF(:to_date, :from_date) + 1
                        END
                    )
                ) AS balance

            FROM company_weekoffs cw
            LEFT JOIN company_policies cp 
                ON cp.id = cw.company_policies_id
            LEFT JOIN regularization reg
                ON reg.company_id = cp.company_id
                AND reg.req_type = :req_type
                AND reg.staff_profile_id = :staff_id
                AND MONTH(reg.from_date) = MONTH(:from_date)
                AND YEAR(reg.from_date) = YEAR(:from_date)
                AND reg.status IN (0,1)
            LEFT JOIN occupation_info oi
                ON oi.id = (
                    SELECT MAX(id)
                    FROM occupation_info
                    WHERE staff_profile_id = :staff_id
                )
            INNER JOIN shift_creation sc
                ON sc.id = oi.shift
            WHERE cp.company_id = :cmpy_id; ";
}


/* ================= OT ================= */
else if ($req_type == '4') {
$query = " SELECT
    sc.start_time,
    sc.end_time,
    (
        SELECT COUNT(id)
        FROM regularization
        WHERE req_type = :req_type
        AND staff_profile_id = :staff_id
        AND company_id = :cmpy_id
        AND status = 1
        AND MONTH(from_date)=MONTH(CURDATE())
        AND YEAR(from_date)=YEAR(CURDATE())
    ) AS current_month_ot_count
FROM occupation_info oi
INNER JOIN shift_creation sc
ON sc.id = oi.shift
WHERE oi.id = (

    SELECT MAX(id)

    FROM occupation_info

    WHERE staff_profile_id = :staff_id

)

";

}



$stmt = $pdo->prepare($query);



$stmt->bindParam(':req_type',$req_type,PDO::PARAM_INT);
$stmt->bindParam(':cmpy_id',$cmpy_id,PDO::PARAM_INT);
$stmt->bindParam(':staff_id',$staff_id);



if ($req_type == '1') {


    $stmt->bindParam(':from_date',$from_Date);
    $stmt->bindParam(':to_date',$to_date);
    $stmt->bindParam(':leave_type',$leave_type);
    $stmt->bindParam(':leave_period',$leave_period,PDO::PARAM_INT);


}


else if ($req_type == '2') {


    $stmt->bindParam(':from_date',$from_Date);


}


else if ($req_type == '3') {


    $stmt->bindParam(':from_date',$from_Date);
    $stmt->bindParam(':to_date',$to_date);
    $stmt->bindParam(':leave_period',$leave_period,PDO::PARAM_INT);


}



$stmt->execute();

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($result);

?>