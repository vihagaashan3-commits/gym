<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: ../main/login.php");
    exit();
}

if(isset($_POST['mark_paid']) && isset($_POST['id'])){

    $paymentId = intval($_POST['id']);

    $stmt = $conn->prepare("UPDATE payments SET status='paid' WHERE id=?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    $stmt->close();
}

header("Location: payments.php");
exit();
?>
