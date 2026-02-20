<?php
session_start();
include("../config/db.php");

// Only admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

/* ==============================
   DASHBOARD STATISTICS
================================ */

// Attendance
$presentCount = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='present'")->fetch_assoc()['total'] ?? 0;
$absentCount  = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='absent'")->fetch_assoc()['total'] ?? 0;

// Revenue
$revenue = $conn->query("
SELECT SUM(pl.price) as total
FROM payments p
JOIN plans pl ON p.plan_id = pl.id
WHERE p.status='paid'
")->fetch_assoc()['total'] ?? 0;

// Total Customers
$totalCustomers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='customer'")->fetch_assoc()['total'] ?? 0;

// Total Trainers
$totalTrainers = $conn->query("SELECT COUNT(*) as total FROM trainers")->fetch_assoc()['total'] ?? 0;

// Active Bookings
$activeBookings = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status='confirmed'")->fetch_assoc()['total'] ?? 0;

// Monthly Registrations
$monthlyRegistrations = [];
for($i=1;$i<=12;$i++){
    $count = $conn->query("
        SELECT COUNT(*) as total 
        FROM users 
        WHERE MONTH(created_at)=$i AND role='customer'
    ")->fetch_assoc()['total'] ?? 0;
    $monthlyRegistrations[] = $count;
}

// Monthly Revenue
$monthlyRevenue = [];
for($i=1;$i<=12;$i++){
    $sum = $conn->query("
        SELECT SUM(pl.price) as total
        FROM payments p
        JOIN plans pl ON p.plan_id = pl.id
        WHERE MONTH(p.created_at)=$i AND p.status='paid'
    ")->fetch_assoc()['total'] ?? 0;
    $monthlyRevenue[] = $sum;
}

// Recent Payments
$recentPayments = $conn->query("
SELECT u.full_name, pl.plan_name, pl.price, p.status
FROM payments p
JOIN users u ON p.user_id = u.id
JOIN plans pl ON p.plan_id = pl.id
ORDER BY p.id DESC
LIMIT 5
");
?>

<link rel="stylesheet" href="../assets/admin_dashboard.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include("../config/sidebar.php"); ?>

<div class="content">

    <h1>📊 Admin Dashboard</h1>

    <!-- Summary Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Members</h3>
            <p><?= $totalCustomers ?></p>
        </div>
        <div class="card">
            <h3>Total Trainers</h3>
            <p><?= $totalTrainers ?></p>
        </div>
        <div class="card">
            <h3>Active Bookings</h3>
            <p><?= $activeBookings ?></p>
        </div>
        <div class="card revenue-card">
            <h3>Total Revenue</h3>
            <p>$<?= number_format($revenue,2) ?></p>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">

        <div class="chart-box">
            <h3>Attendance Overview</h3>
            <canvas id="attendanceChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Monthly Registrations</h3>
            <canvas id="registrationChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Monthly Revenue</h3>
            <canvas id="revenueChart"></canvas>
        </div>

    </div>

    <!-- Recent Payments -->
    <div class="recent-section">
        <h3>Recent Payments</h3>
        <table>
            <tr>
                <th>Member</th>
                <th>Plan</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            <?php while($row=$recentPayments->fetch_assoc()){ ?>
                <tr>
                    <td><?= $row['full_name'] ?></td>
                    <td><?= $row['plan_name'] ?></td>
                    <td>$<?= $row['price'] ?></td>
                    <td>
                        <span class="status <?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>

<script>
new Chart(document.getElementById('attendanceChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent'],
        datasets:[{
            data:[<?= $presentCount ?>, <?= $absentCount ?>],
            backgroundColor:['#10b981','#ef4444']
        }]
    }
});

new Chart(document.getElementById('registrationChart'), {
    type: 'bar',
    data: {
        labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets:[{
            label:'Customer Registrations',
            data:[<?= implode(',', $monthlyRegistrations); ?>],
            backgroundColor:'#3b82f6'
        }]
    },
    options:{responsive:true, scales:{y:{beginAtZero:true}}}
});

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets:[{
            label:'Revenue',
            data:[<?= implode(',', $monthlyRevenue); ?>],
            borderColor:'#10b981',
            backgroundColor:'rgba(16,185,129,0.2)',
            fill:true,
            tension:0.4
        }]
    },
    options:{responsive:true}
});
</script>
