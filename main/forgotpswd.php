<?php
session_start();
include("../config/db.php");

$error = '';
$success = '';

if(isset($_POST['submit'])){
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50));

        // Save token & expiry
        $stmt2 = $conn->prepare("UPDATE users SET reset_token=?, reset_token_expiry=DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id=?");
        $stmt2->bind_param("si",$token,$user['id']);
        $stmt2->execute();
        $stmt2->close();

        // Generate clickable link
        $resetLink = "http://localhost/gym/main/reset_password.php?token=$token";
        $success = "Password reset link generated. Click here: <a href='$resetLink' class='link-btn'>Reset Password</a>";
    } else {
        $error = "Email not found!";
    }
    $stmt->close();
}
?>

<link rel="stylesheet" href="../assets/style.css">

<div class="auth-container">
    <div class="auth-box">
        <h2>Forgot Password</h2>

        <?php if($error){ echo "<p class='error'>$error</p>"; } ?>
        <?php if($success){ echo "<p class='success'>$success</p>"; } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button name="submit">Generate Reset Link</button>
        </form>

        <div class="auth-links">
            <p>Remembered your password? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</div>
