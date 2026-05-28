<?PHP
session_start();

include('../../ajaxconfig.php');

$user_id = $_SESSION["user_id"];
$response = array();

$qry = $pdo->prepare("SELECT user_name FROM users where `id` = ?");
$qry->execute(array($user_id));

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch()) {
        $response['user_name'] = $row['user_name'];
    }
}

echo json_encode($response);