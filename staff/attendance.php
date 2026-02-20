<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only staff
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'staff'){
    header("Location: ../main/login.php");
    exit();
}

// Fetch customers
$users = $conn->query("SELECT id, full_name FROM users WHERE role='customer'");

// Attendance records
$attendance_list = $conn->query("
    SELECT a.*, u.full_name 
    FROM attendance a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.date DESC
");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/staff_attendance.css">

<div class="main-content">

    <div class="page-header">
        <h1>📋 Attendance Management</h1>
        <p>Mark and manage member attendance records</p>
    </div>

    <!-- Attendance Form Card -->
    <div class="card">
        <h2>Mark Attendance</h2>
        <form method="POST" action="attendance.php" class="attendance-form">

            <div class="form-group">
                <label>Select Member</label>
                <select name="user_id" required>
                    <option value="">Choose Member</option>
                    <?php while($u = $users->fetch_assoc()){ ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo $u['full_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>
            </div>

            <button type="submit" name="mark_attendance" class="btn-primary">
                Mark Attendance
            </button>

        </form>
    </div>

    <!-- Attendance Records -->
    <div class="card">
        <h2>Attendance Records</h2>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Member</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $attendance_list->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td>
                            <?php if($row['status'] == 'present'){ ?>
                                <span class="badge present">Present</span>
                            <?php } else { ?>
                                <span class="badge absent">Absent</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

</div>
