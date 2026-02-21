<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only customer access
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// ==========================
// Handle plan selection
// ==========================
if(isset($_POST['choose_plan'])){
    $plan_id = $_POST['plan_id'];

    // Check if customer already has an active payment
    $check = $conn->query("SELECT * FROM payments WHERE user_id=$userId AND status='pending'");
    if($check->num_rows > 0){
        $msg = "You already have a pending plan. Complete payment first.";
    } else {
        // Insert into payments
        $stmt = $conn->prepare("INSERT INTO payments (user_id, plan_id, status) VALUES (?,?,?)");
        $status = 'pending';
        $stmt->bind_param("iis", $userId, $plan_id, $status);
        $stmt->execute();
        $stmt->close();
        $msg = "Plan selected! Awaiting admin confirmation.";
    }
}

// ==========================
// Fetch all plans
// ==========================
$plans = $conn->query("SELECT * FROM plans");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/customer-plans.css">

<div class="content">
    <h1>Available Plans</h1>

    <?php if(isset($msg)){ ?>
        <div class="alert"><?php echo $msg; ?></div>
    <?php } ?>

    <div class="plans-grid">
        <?php while($row = $plans->fetch_assoc()){ ?>
            <div class="plan-card">
                <h3><?= $row['plan_name'] ?></h3>
                <p><strong>Duration:</strong> <?= $row['duration'] ?> Months</p>
                <p><strong>Price:</strong> Rs <?= $row['price'] ?></p>
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?= $row['id'] ?>">
                    <button name="choose_plan" class="btn primary-btn">Choose Plan</button>
                </form>
            </div>
        <?php } ?>
    </div>
</div>
