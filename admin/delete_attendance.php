<?php
session_start();
include("../config/db.php");


if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

if(!isset($_GET['id'])) exit("No record ID");

$id = $_GET['id'];
$conn->query("DELETE FROM attendance WHERE id=$id");
header("Location: attendance.php");
exit();
?>

