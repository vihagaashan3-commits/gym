<?php
session_start();
include("../config/db.php");

$error = '';
$success = '';

if(!isset($_GET['token'])){
    die("Invalid link!");
}

$token = $_GET['token'];

// Validate token
$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=? AND reset_token_expiry > NOW()");
$stmt->bind_param("s",$token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows != 1){
    die("Invalid or expired token!");
}

$user = $result->fetch_assoc();
$userId = $user['id'];

// Handle password reset
if(isset($_POST['reset'])){
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt2 = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_token_expiry=NULL WHERE id=?");
    $stmt2->bind_param("si",$password,$userId);
    if($stmt2->execute()){
        $success = "Password reset successfully! <a href='login.php'>Login Here</a>";
    } else {
        $error = "Error resetting password.";
    }
    $stmt2->close();
}
$stmt->close();
?>

<link rel="stylesheet" href="../assets/style.css">

<div class="auth-container">
    <div class="auth-box">
        <h2>Reset Password</h2>

        <?php if($error){ echo "<p class='error'>$error</p>"; } ?>
        <?php if($success){ echo "<p class='success'>$success</p>"; } ?>

        <form method="POST">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button name="reset">Reset Password</button>
        </form>
    </div>
</div>
