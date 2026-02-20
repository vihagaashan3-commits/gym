<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

// Only staff access
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'staff'){
    header("Location: ../main/login.php");
    exit();
}

$staffId = $_SESSION['user']['id'];

/* ===============================
   FETCH STAFF DETAILS
================================= */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $staffId);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ===============================
   UPDATE PROFILE
================================= */
if(isset($_POST['update_profile'])){
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $phone, $staffId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['full_name'] = $name;
    header("Location: dashboard.php?success=profile_updated");
    exit();
}

/* ===============================
   PROFILE PICTURE UPLOAD
================================= */
if(isset($_POST['upload_pic'])){
    if(!empty($_FILES['profile_pic']['name'])){

        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $target = "../assets/uploads/" . $fileName;

        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)){

            $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
            $stmt->bind_param("si", $fileName, $staffId);
            $stmt->execute();
            $stmt->close();

            header("Location: dashboard.php?success=picture_updated");
            exit();
        }
    }
}

/* ===============================
   FETCH CUSTOMERS
================================= */
$members = $conn->query("
    SELECT id, full_name, email, phone 
    FROM users 
    WHERE role='customer'
    ORDER BY full_name
");

if(!$members){
    die("Customer Query Error: " . $conn->error);
}

?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/staff_profile.css">

<div class="content">
    <?php
// Dashboard Stats


/* ===============================
   DASHBOARD STATS (SAFE VERSION)
================================= */

function getCountSafe($conn, $sql){
    $result = $conn->query($sql);
    if($result){
        $row = $result->fetch_assoc();
        return $row['total'];
    } else {
        return 0; // If table doesn't exist, return 0 instead of crashing
    }
}

// Total Customers
$totalCustomers = getCountSafe(
    $conn,
    "SELECT COUNT(*) as total FROM users WHERE role='customer'"
);

// Active Plans (only if table exists)
$activePlans = getCountSafe(
    $conn,
    "SELECT COUNT(*) as total FROM subscriptions WHERE status='active'"
);

// Pending Payments
$pendingPayments = getCountSafe(
    $conn,
    "SELECT COUNT(*) as total FROM payments WHERE status='pending'"
);

// Completed Payments
$completedPayments = getCountSafe(
    $conn,
    "SELECT COUNT(*) as total FROM payments WHERE status='completed'"
);
?>



    <h1>👨‍🏫 Staff Profile</h1>

    <?php if(isset($_GET['success'])){ ?>
        <div class="success-msg">Profile updated successfully!</div>
    <?php } ?>

    <!-- PROFILE CARD -->
    <div class="profile-card">

        <div class="profile-left">
            <?php if(!empty($staff['profile_pic'])){ ?>
                <img src="../assets/uploads/<?php echo $staff['profile_pic']; ?>" class="profile-pic">
            <?php } else { ?>
                <img src="../assets/default-user.png" class="profile-pic">
            <?php } ?>

            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="profile_pic" required>
                <button type="submit" name="upload_pic" class="btn">Change Picture</button>
            </form>
        </div>

        <div class="profile-right">
            <h2><?php echo $staff['full_name']; ?></h2>
            <p><strong>Email:</strong> <?php echo $staff['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $staff['phone']; ?></p>

            <hr>

            <h3>Edit Profile</h3>
            <form method="POST" class="edit-form">
                <input type="text" name="full_name" value="<?php echo $staff['full_name']; ?>" required>
                <input type="text" name="phone" value="<?php echo $staff['phone']; ?>" required>
                <button type="submit" name="update_profile" class="btn primary-btn">Update Profile</button>
            </form>
        </div>

    </div>

    <!-- ASSIGNED CUSTOMERS -->
    <div class="customers-section">
        <h2>Assigned Customers</h2>

        <?php if($members->num_rows > 0){ ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
            <?php while($m = $members->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $m['full_name']; ?></td>
                <td><?php echo $m['email']; ?></td>
                <td><?php echo $m['phone']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
            <p class="empty">No assigned customers found.</p>
        <?php } ?>
    </div>

</div>
