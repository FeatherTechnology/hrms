<?php
// Save staff general feedback with comments and optional attachment.

require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['attachment']['name'])) {
    $path = "../../uploads/general_feedback";
    $attachment = $_FILES['attachment']['name'];
    $pic_temp = $_FILES['attachment']['tmp_name'];
    $picfolder = $path . $attachment;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $attachment = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $attachment)) {
        //this loop will continue until it generates a unique file name
        $attachment = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $attachment);
} else {
    $attachment = '';
}
$feedback_name = $_POST['feedback_name'];
$commants = $_POST['commants'];

$qry = $pdo->query("INSERT INTO `staff_general_feedback`( `general_feedback_id`,`commants`,`attachment`,`insert_login_id`,`created_date`) VALUES ('$feedback_name','$commants','$attachment','$user_id',now())");

if($qry){
$result = 1;
}else{
$result = 2;
}

echo json_encode($result);
