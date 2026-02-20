<?php
session_start();
include("../config/db.php");

$error = "";

if(isset($_POST['login'])){

    // Sanitize inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)){
        $error = "Please enter email and password.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){

            $user = $result->fetch_assoc();

            // Verify hashed password
            if(password_verify($password, $user['password'])){

                // Regenerate session ID (security best practice)
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'profile_pic' => $user['profile_pic']
                ];

                // Role based redirect
                if($user['role'] === "admin"){
                    header("Location: ../admin/dashboard.php");
                }
                elseif($user['role'] === "staff"){
                    header("Location: ../staff/dashboard.php");
                }
                else{
                    header("Location: ../customer/dashboard.php");
                }
                exit();

            } else {
                $error = "Invalid email or password!";
            }

        } else {
            $error = "Invalid email or password!";
        }

        $stmt->close();
    }
}
?>


<link rel="stylesheet" href="../assets/style.css">

    <div class="auth-container">
        <div class="auth-box">
            <h2>Welcome Back</h2>

            <?php if($error){ ?>
                <div class="error-box"><?php echo $error; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" name="login" class="btn-primary">
                    Login
                </button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p><a href="forgotpswd.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>


