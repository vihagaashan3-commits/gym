<?php
// Step 2a: Session
if(session_status() == PHP_SESSION_NONE) session_start();

include("../config/db.php");

// Only admin access
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

// Step 2b: Fetch all payments with user + plan info
$payments = $conn->query("
SELECT p.id, u.full_name, u.email, pl.plan_name, pl.price AS amount, p.status
FROM payments p
JOIN users u ON p.user_id = u.id
JOIN plans pl ON p.plan_id = pl.id
");
?>

<link rel="stylesheet" href="../assets/payments.css">
<?php include("../config/sidebar.php"); ?>

<div class="content">
    <h1>Payments & Plans</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row=$payments->fetch_assoc()){ ?>
            <tr>
                <td><?= $row['full_name'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['plan_name'] ?></td>
                <td><?= $row['amount'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if($row['status']=='pending'){ ?>
                       <form method="POST" action="mark_paid.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <button type="submit" name="mark_paid" class="btn update-btn">
        Mark Paid
    </button>
</form>

                    <?php } else { ?>
                        <span class="paid-label">Paid</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
