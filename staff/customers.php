<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only staff
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'staff'){
    header("Location: ../main/login.php");
    exit();
}

// Fetch customers
$members = $conn->query("SELECT id, full_name, email, phone FROM users WHERE role='customer'");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/staff_customer.css">

<div class="main-content">

    <div class="page-header">
        <h1>👥 My Customers</h1>
        <p>Manage and view customer details</p>
    </div>

    <!-- Search Box -->
    <div class="card">
        <input type="text" id="searchInput" placeholder="🔍 Search customer..." class="search-box">
    </div>

    <!-- Customers Table -->
    <div class="card">
        <table class="customers-table" id="customersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php while($m = $members->fetch_assoc()){ ?>
                    <tr>
                        <td>#<?php echo $m['id']; ?></td>
                        <td class="member-cell">
                            <div class="avatar">
                                <?php echo strtoupper(substr($m['full_name'],0,1)); ?>
                            </div>
                            <?php echo $m['full_name']; ?>
                        </td>
                        <td><?php echo $m['email']; ?></td>
                        <td><?php echo $m['phone']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<script>
// Simple search filter
document.getElementById("searchInput").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#customersTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
});
</script>
