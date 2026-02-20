<?php
include("../config/db.php");

if(isset($_POST['register'])){
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $phone = $_POST['phone'];

    $uploads_dir = "../assets/uploads";
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);


    $role = "customer";

$stmt = $conn->prepare("INSERT INTO users(full_name,email,password,role,phone) VALUES(?,?,?,?,?)");
$stmt->bind_param("sssss", $name, $email, $password, $role, $phone);


    if($stmt->execute()){
        header("Location: login.php");
        exit();
    } else {
        $error = $stmt->error;
    }
}
?>

<link rel="stylesheet" href="../assets/style.css">

<div class="auth-container">
    <div class="auth-box">
        <h2>Register</h2>

        <?php if(isset($error)){ echo "<p class='error'>$error</p>"; } ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
           
            
            <button name="register">Register</button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</div>
