<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'staff'){
    header("Location: ../main/login.php");
    exit();
}

$staffId = $_SESSION['user']['id'];

// Update profile details
if(isset($_POST['update_profile'])){
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $phone, $staffId);
    $stmt->execute();
    $stmt->close();
}

// Update profile picture
if(isset($_POST['update_pic'])){
    $fileName = time() . "_" . $_FILES['profile_pic']['name'];
    $target = "../assets/uploads/" . $fileName;

    if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)){
        $conn->query("UPDATE users SET profile_pic='$fileName' WHERE id='$staffId'");
    }
}

header("Location: dashboard.php");
exit();
