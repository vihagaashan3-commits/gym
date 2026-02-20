<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}

$customerId = $_SESSION['user']['id'];
$message = '';

if(isset($_POST['upload_pic'])){
    $uploads_dir = "../assets/uploads";
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);

    $originalName = $_FILES['profile_pic']['name'];
    $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);

    // ✅ File type validation
    $allowed = ['jpg','jpeg','png','gif'];
    if(!in_array(strtolower($fileExt), $allowed)){
        $message = "Only JPG, JPEG, PNG, GIF files are allowed!";
    } elseif($_FILES['profile_pic']['size'] > 2*1024*1024){
        $message = "File size must be less than 2MB!";
    } else {
        $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) . "." . $fileExt;
        $tmp = $_FILES['profile_pic']['tmp_name'];

        if(move_uploaded_file($tmp, $uploads_dir . "/" . $safeName)){
            $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $stmt->bind_param("si",$safeName,$customerId);
            $stmt->execute();

            $_SESSION['user']['profile_pic'] = $safeName;
            $message = "Profile picture updated successfully!";
        } else {
            $message = "Failed to upload image!";
        }
    }
}

header("Location: dashboard.php?msg=" . urlencode($message));
exit();
?>
