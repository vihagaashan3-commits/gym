<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only customer
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// -------------------------
// Fetch all payments
// -------------------------
$stmt = $conn->prepare("
    SELECT p.id, pl.plan_name, pl.duration, pl.price AS amount, p.status
    FROM payments p
    JOIN plans pl ON p.plan_id = pl.id
    WHERE p.user_id=?
    ORDER BY p.id DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$payments = $stmt->get_result();
$stmt->close();

// -------------------------
// Summary counts using correct status values
// -------------------------
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM payments WHERE user_id=? AND status='paid'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$paidCount = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM payments WHERE user_id=? AND status='pending'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$pendingCount = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/customer_payments.css">

<div class="content">

    <h1>💳 My Payments</h1>

    <!-- Summary Cards -->
    <div class="payment-summary">
        <div class="summary-card completed-card">
            <h3>Completed Payments</h3>
            <p><?php echo $paidCount; ?></p>
        </div>

        <div class="summary-card pending-card">
            <h3>Pending Payments</h3>
            <p><?php echo $pendingCount; ?></p>
        </div>
    </div>

    <!-- Payment Cards -->
    <div class="payment-list">
        <?php if($payments && $payments->num_rows > 0){ ?>
            <?php while($row=$payments->fetch_assoc()){ ?>
                <div class="payment-card">
                    <div class="payment-info">
                        <h3><?php echo $row['plan_name']; ?></h3>
                        <p><strong>Duration:</strong> <?php echo $row['duration']; ?> Months</p>
                        <p><strong>Amount:</strong> $<?php echo $row['amount']; ?></p>
                    </div>

                    <div class="payment-status">
                        <?php if($row['status']=='paid'){ ?>
                            <span class="status paid">Paid</span>
                        <?php } else { ?>
                            <span class="status pending">Pending</span>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="no-payments">No payments found. Please select a plan to start.</p>
        <?php } ?>
    </div>

</div>
