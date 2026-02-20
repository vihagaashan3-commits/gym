<?php
session_start();
include("../config/db.php");

// Only admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

if(!isset($_GET['id'])) exit("No user ID");

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id=$id");
header("Location: manage_users.php");
exit();
?>
