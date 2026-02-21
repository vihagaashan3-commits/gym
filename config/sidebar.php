<?php
// Start session if not already
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

$user = $_SESSION['user'] ?? null;

if(!$user){
    header("Location: ../main/login.php");
    exit();
}

// Determine role
$role = $user['role'];
?>

<link rel="stylesheet" href="../assets/style.css">

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Titanium Fitness Suite</h2>
        <p><?php echo ucfirst($role); ?>: <?php echo $user['full_name']; ?></p>
    </div>

    <div class="sidebar-menu">
        <?php if($role == 'admin'){ ?>
            <a href="../admin/dashboard.php">Dashboard</a>
            <a href="../admin/manage_users.php">Manage Members</a>
            <a href="../admin/attendance.php">Attendance</a>
            <a href="../admin/plans.php">Membership Plans</a>
            <a href="../admin/manage_plans.php">Schedule/Diet</a>
            <a href="../admin/manage_trainers.php">Manage Trainers</a>
            <a href="../admin/payments.php">Payments</a>
            

        <?php } elseif($role == 'staff'){ ?>
            <a href="../staff/dashboard.php">Dashboard</a>
            <a href="../staff/attendance.php">Attendance</a>
            <a href="../staff/trainer_schedule.php">Schedule</a>
            <a href="../staff/customers.php">Customers</a>

        <?php } elseif($role == 'customer'){ ?>
            <a href="../customer/dashboard.php">Dashboard</a>
            <a href="../customer/attendance.php">My Attendance</a>
            <a href="../customer/plan.php">Payment Plans</a>
            <a href="../customer/schedulediet.php">Schedule/Diet</a>
            <a href="../customer/book_trainer.php">Book Trainer</a>
            <a href="../customer/payments.php">My Payments</a>
        <?php } ?>
    </div>

    <div class="sidebar-footer">
        <a href="../main/logout.php" class="logout">Logout</a>
    </div>
</div>
