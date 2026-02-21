<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

// Only customer
if($_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}


$customerId = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();


// User details
$user = $conn->query("SELECT * FROM users WHERE id='$customerId'")->fetch_assoc();

// Attendance
$attendance = $conn->query("
    SELECT * FROM attendance 
    WHERE user_id='$customerId' 
    ORDER BY date DESC
");

// Plan & Payment
$plan = $conn->query("
    SELECT pl.plan_name, pl.duration, pl.price, p.status
    FROM payments p
    JOIN plans pl ON p.plan_id = pl.id
    WHERE p.user_id='$customerId'
    ORDER BY p.id DESC
    LIMIT 1
")->fetch_assoc();
?>

<link rel="stylesheet" href="../assets/customer_dashboard.css">
<?php include("../config/sidebar.php"); ?>

<div class="content">

    <!-- PROFILE HEADER -->
    <div class="profile-header">

        <?php if(!empty($user['profile_pic'])){ ?>
            <img src="../assets/uploads/<?php echo $user['profile_pic']; ?>" class="profile-pic">
        <?php } else { ?>
            <img src="../assets/default.png" class="profile-pic">
        <?php } ?>

        <h2><?php echo $user['full_name']; ?></h2>
        <p class="email"><?php echo $user['email']; ?></p>
        <p class="phone"><?= $customer['phone'] ?></p>

        <form method="POST" enctype="multipart/form-data" action="update_profile_pic.php">
            <input type="file" name="profile_pic" required>
            <button name="upload_pic" class="btn">Change Picture</button>
        </form>

    </div>

    <!-- PLAN SECTION -->
    <div class="profile-section">
        <h3 style="color:white;">Membership Plan</h3>

        <?php if($plan){ ?>
            <div class="info-box">
                <p><strong>Plan:</strong> <?php echo $plan['plan_name']; ?></p>
                <p><strong>Duration:</strong> <?php echo $plan['duration']; ?> Months</p>
                <p><strong>Price:</strong> Rs<?php echo $plan['price']; ?></p>
                <p><strong>Status:</strong> 
                    <span class="<?php echo $plan['status']; ?>">
                        <?php echo ucfirst($plan['status']); ?>
                    </span>
                </p>
            </div>
        <?php } else { ?>  
            <p>No active plan.</p>
            <a href="plans.php" class="btn primary-btn">Choose Plan</a>
        <?php } ?>
    </div>

    <!-- ATTENDANCE SECTION -->
    <div class="profile-section">
        <h3 style="color:white">Attendance History</h3>

        <table class="table">
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php while($a = $attendance->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $a['date']; ?></td>
                <td>
                    <span class="<?php echo $a['status']; ?>">
                        <?php echo ucfirst($a['status']); ?>
                    </span>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>
