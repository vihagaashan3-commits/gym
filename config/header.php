<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: ../config/login.php");
}
?>
<link rel="stylesheet" href="../assets/style.css">
