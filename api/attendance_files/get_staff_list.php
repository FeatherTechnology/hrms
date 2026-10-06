<?php

include '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'] ?? 0;
$company_id   = $_POST['cmpy_id'] ?? '';
$dept_id      = $_POST['dep_name'] ?? '';
$branch_id    = $_POST['branch_name'] ?? '';
$request_type = $_POST['request_type'] ?? 'branch';

if ($request_type == 'department') {
    if (empty($company_id) || empty($dept_id)) {
        echo json_encode(['staff' => [],'show_all' => false ]);
        exit;
    }
} else {
    if (empty($company_id) || empty($dept_id) || empty($branch_id)) {
        echo json_encode(['staff' => [],'show_all' => false ]);
        exit;
    }
}
$userQry = $pdo->prepare("SELECT report_access, user_type, director_name, staff_name_id FROM users WHERE id = ?");
$userQry->execute([$user_id]);
$user = $userQry->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['staff' => [],'show_all' => false]);
    exit;
}
$report_access = (int)($user['report_access'] ?? 1);
$user_type     = (int)($user['user_type'] ?? 0);
$director_id   = $user['director_name'] ?? '';
$staff_id      = $user['staff_name_id'] ?? '';

if ($report_access != 2) {
    if ($request_type == 'department') {
        $sql = " SELECT sc.id,sc.staff_name
            FROM staff_creation sc
            INNER JOIN (SELECT oi1.staff_profile_id,oi1.company_id,oi1.department
                FROM occupation_info oi1
                INNER JOIN (
                    SELECT staff_profile_id,MAX(id) AS max_id
                    FROM occupation_info
                    GROUP BY staff_profile_id
                ) latest ON latest.staff_profile_id = oi1.staff_profile_id AND latest.max_id = oi1.id) oi ON oi.staff_profile_id = sc.id
            WHERE oi.company_id = :company_id AND oi.department = :department_id ORDER BY sc.staff_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':company_id',$company_id,PDO::PARAM_INT);
        $stmt->bindValue(':department_id',$dept_id,PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([ 'staff' => $result, 'show_all' => true ]);
        exit;
    }

    $sql = "SELECT sc.id,sc.staff_name
        FROM staff_creation sc
        INNER JOIN (
            SELECT
                oi1.staff_profile_id,
                oi1.company_id,
                oi1.branch_id,
                oi1.department
            FROM occupation_info oi1
            INNER JOIN (
                SELECT
                    staff_profile_id,
                    MAX(id) AS max_id
                FROM occupation_info
                GROUP BY staff_profile_id
            ) latest
                ON latest.staff_profile_id = oi1.staff_profile_id
                AND latest.max_id = oi1.id
        ) oi
            ON oi.staff_profile_id = sc.id
        WHERE oi.company_id = :company_id
          AND oi.branch_id = :branch_id
          AND oi.department = :department_id
        ORDER BY sc.staff_name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':company_id',$company_id,PDO::PARAM_INT);
    $stmt->bindValue(':branch_id',$branch_id,PDO::PARAM_INT);
    $stmt->bindValue(':department_id', $dept_id,PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['staff' => $result,'show_all' => true]);
    exit;
}

if ($user_type == 1 && !empty($director_id)) {

    $sql = "WITH RECURSIVE reporting_tree AS (
            SELECT DISTINCT
                CAST(rpm1.reporting_staff AS UNSIGNED) AS staff_id
            FROM reporting_person_mapping rpm1
            INNER JOIN reporting_person rp1
                ON rp1.id = rpm1.reporting_person_id
            WHERE rp1.user_type = 1
              AND rp1.director_id = :director_id
              AND rpm1.reporting_staff IS NOT NULL
              AND rpm1.reporting_staff != ''

            UNION

            SELECT DISTINCT
                CAST(rpm2.reporting_staff AS UNSIGNED) AS staff_id
            FROM reporting_person_mapping rpm2
            INNER JOIN reporting_person rp2
                ON rp2.id = rpm2.reporting_person_id
            INNER JOIN reporting_tree rt
                ON rt.staff_id = rp2.reporting_person
            WHERE rp2.user_type = 2
              AND rpm2.reporting_staff IS NOT NULL
              AND rpm2.reporting_staff != ''
        ),
        latest_occupation AS (
            SELECT
                oi1.staff_profile_id,
                oi1.company_id,
                oi1.branch_id,
                oi1.department
            FROM occupation_info oi1
            INNER JOIN (
                SELECT
                    staff_profile_id,
                    MAX(id) AS max_id
                FROM occupation_info
                GROUP BY staff_profile_id
            ) latest
                ON latest.staff_profile_id = oi1.staff_profile_id
                AND latest.max_id = oi1.id
        )
        SELECT
            sc.id,
            sc.staff_name
        FROM reporting_tree rt
        INNER JOIN staff_creation sc
            ON sc.id = rt.staff_id
        INNER JOIN latest_occupation oi
            ON oi.staff_profile_id = sc.id
        WHERE oi.company_id = :company_id
          AND oi.department = :department_id";

    if ($request_type == 'branch') {
        $sql .= "AND oi.branch_id = :branch_id";
    }

    $sql .= " ORDER BY sc.staff_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':director_id',$director_id,PDO::PARAM_INT);
    $stmt->bindValue(':company_id',$company_id,PDO::PARAM_INT);
    $stmt->bindValue(':department_id',$dept_id,PDO::PARAM_INT);
    if ($request_type == 'branch') {
        $stmt->bindValue(':branch_id',$branch_id,PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([ 'staff' => $result,'show_all' => false ]);
    exit;
}

if ($user_type == 2 && !empty($staff_id)) {

    $sql = "SELECT DISTINCT
            sc.id,
            sc.staff_name
        FROM reporting_person_mapping rpm
        INNER JOIN reporting_person rp ON rp.id = rpm.reporting_person_id
        INNER JOIN staff_creation sc ON sc.id = rpm.reporting_staff
        INNER JOIN (
            SELECT oi1.staff_profile_id,
                oi1.company_id,
                oi1.branch_id,
                oi1.department
            FROM occupation_info oi1
            INNER JOIN (
                SELECT
                    staff_profile_id,
                    MAX(id) AS max_id
                FROM occupation_info
                GROUP BY staff_profile_id
            ) latest
                ON latest.staff_profile_id = oi1.staff_profile_id
                AND latest.max_id = oi1.id
        ) oi ON oi.staff_profile_id = sc.id
        WHERE rp.user_type = 2 AND rp.reporting_person = :staff_id AND oi.company_id = :company_id AND oi.department = :department_id";

    if ($request_type == 'branch') {
        $sql .= "AND oi.branch_id = :branch_id";
    }

    $sql .= "ORDER BY sc.staff_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':staff_id',$staff_id,PDO::PARAM_INT);
    $stmt->bindValue(':company_id',$company_id,PDO::PARAM_INT);
    $stmt->bindValue( ':department_id', $dept_id, PDO::PARAM_INT);
    if ($request_type == 'branch') {
        $stmt->bindValue(':branch_id',$branch_id,PDO::PARAM_INT);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['staff' => $result,'show_all' => false]);

    exit;
}
echo json_encode(['staff' => [],'show_all' => false]);

?>