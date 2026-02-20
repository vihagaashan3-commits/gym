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
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){
    $name=$_POST['full_name'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $role=$_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=? WHERE id=?");
    $stmt->bind_param("ssssi",$name,$email,$phone,$role,$id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/edit_user.css">

<?php include("../config/sidebar.php"); ?>

<div class="dashboard-wrapper">
    <div class="content">
        <h2>Edit User</h2>

        <form method="POST" class="edit-form">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label>Role</label>
            <select name="role" required>
                <option value="customer" <?php if($user['role']=="customer") echo "selected";?>>Customer</option>
                <option value="staff" <?php if($user['role']=="staff") echo "selected";?>>Staff</option>
                <option value="admin" <?php if($user['role']=="admin") echo "selected";?>>Admin</option>
            </select>

            <button type="submit" name="update" class="btn update-btn">Update User</button>
        </form>
    </div>
</div>
