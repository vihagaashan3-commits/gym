<?php
session_start();
include("../config/db.php");

// Only admin access
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

// Mark attendance
if(isset($_POST['mark_attendance'])){
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Check if attendance already exists
    $check = $conn->prepare("SELECT * FROM attendance WHERE user_id=? AND date=?");
    $check->bind_param("is",$user_id,$date);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows == 0){
        $stmt = $conn->prepare("INSERT INTO attendance(user_id,date,status) VALUES(?,?,?)");
        $stmt->bind_param("iss",$user_id,$date,$status);
        $stmt->execute();
        $message = "Attendance marked successfully!";
    } else {
        $message = "Attendance for this user on this date already exists.";
    }
}

// Fetch all customers
$users = $conn->query("SELECT id, full_name FROM users WHERE role='customer'");

// Fetch all attendance
$attendance_list = $conn->query("SELECT a.*, u.full_name FROM attendance a JOIN users u ON a.user_id=u.id ORDER BY a.date DESC");

// Count Present / Absent for chart
$presentCount = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='present'")->fetch_assoc()['total'];
$absentCount = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='absent'")->fetch_assoc()['total'];
?>

<link rel="stylesheet" href="../assets/style.css">
<?php include("../config/sidebar.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<div class="content">
    <h1>Attendance Management</h1>

    <?php if(isset($message)){ echo "<p style='color:green;'>$message</p>"; } ?>

    <div class="attendance-form">
        <h3>Mark Attendance</h3>
        <form method="POST">
            <select name="user_id" required>
                <option value="">Select Member</option>
                <?php while($u = $users->fetch_assoc()){ ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo $u['full_name']; ?></option>
                <?php } ?>
            </select>
            <input type="date" name="date" required>
            <select name="status" required>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
            <button name="mark_attendance">Mark Attendance</button>
        </form>
    </div>

    <div class="attendance-chart">
        <h3>Attendance Overview</h3>
        <canvas id="attendanceChart"></canvas>
    </div>

    <h3>All Attendance Records</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Member</th><th>Date</th><th>Status</th><th>Action</th>
        </tr>
        <?php while($row = $attendance_list->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="delete_attendance.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this attendance?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
// Attendance Pie Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
