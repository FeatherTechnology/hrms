<?php
require '../../ajaxconfig.php';
@session_start();
$userid = $_SESSION['user_id'];

if(isset($_FILES['home_upload'])){

    $today = date("Y-m-d H:i:s");

    // STEP 1: Delete old media for today
    $qry = $pdo->query("SELECT media_path FROM home_upload ");
                            
    if($qry->rowCount() > 0){
        $row = $qry->fetch();
        $old_file = "../../uploads/home_upload/".$row['media_path'];
        if(file_exists($old_file)){
            unlink($old_file); // remove existing file
        }
        $pdo->query("DELETE FROM home_upload ");
    }

    // STEP 2: Upload new file
    $file = $_FILES['home_upload'];
    $filename = time() . "_" . $file['name'];
    $upload_path = "../../uploads/home_upload/$filename";

    if(!is_dir("../../uploads/home_upload")){
        mkdir("../../uploads/home_upload", 0777, true);
    }

    move_uploaded_file($file['tmp_name'], $upload_path);

    // detect media type
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if(in_array($ext, ['jpg','jpeg','png','gif'])) {
        $media_type = "image";
    } elseif(in_array($ext, ['mp4','mov','mkv'])) {
        $media_type = "video";
    } elseif(in_array($ext, ['mp3','wav'])) {
        $media_type = "audio";
    } else {
        echo "invalid_file";
        exit;
    }

    // STEP 3: Insert new entry
    $file_path_db = $filename;

    $stmt = $pdo->prepare("
        INSERT INTO home_upload (user_id, media_path, media_type, upload_date) 
        VALUES (:userid, :path, :type, :today)
    ");

    $stmt->execute([
        ':userid' => $userid,
        ':path' => $file_path_db,
        ':type' => $media_type,
        ':today' => $today
    ]);

    echo "success";

} else {
    echo "no_file";
}
?>
