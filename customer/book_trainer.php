<?php
session_start();
include("../config/db.php");
if($_SESSION['user']['role']!='customer'){ header("Location: ../main/login.php"); exit(); }

$userId = $_SESSION['user']['id'];

// Fetch trainers & schedules
$trainers = $conn->query("SELECT * FROM trainers ORDER BY full_name");

if(isset($_POST['book'])){
    $trainer_id = $_POST['trainer_id'];
    $day = $_POST['day'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO bookings(customer_id,trainer_id,day,start_time,end_time) VALUES(?,?,?,?,?)");
    $stmt->bind_param("iisss",$userId,$trainer_id,$day,$start,$end);
    $stmt->execute();
    $success = "Trainer booked successfully!";
}

$schedules = $conn->query("SELECT ts.*, t.full_name FROM trainer_schedule ts JOIN trainers t ON ts.trainer_id=t.id ORDER BY ts.day,ts.start_time");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/book_trainer.css">

<div class="content">
<h1>Book a Trainer</h1>

<?php if(isset($success)) echo "<div class='success-msg'>$success</div>"; ?>

<form method="POST" class="booking-form">
<select name="trainer_id" required>
<option value="">Select Trainer</option>
<?php while($t=$trainers->fetch_assoc()){ ?>
<option value="<?= $t['id'] ?>"><?= $t['full_name'] ?> (<?= str_replace('_',' ',$t['specialization']) ?>)</option>
<?php } ?>
</select>

<select name="day" required>
<option value="">Select Day</option>
<option>Monday</option><option>Tuesday</option><option>Wednesday</option>
<option>Thursday</option><option>Friday</option><option>Saturday</option><option>Sunday</option>
</select>

<input type="time" name="start_time" required>
<input type="time" name="end_time" required>
<button name="book" class="btn primary-btn">Book Trainer</button>
</form>

<h2>Available Schedules</h2>
<table>
<tr><th>Trainer</th><th>Day</th><th>Start</th><th>End</th></tr>
<?php while($s=$schedules->fetch_assoc()){ ?>
<tr>
<td><?= $s['full_name'] ?></td>
<td><?= $s['day'] ?></td>
<td><?= $s['start_time'] ?></td>
<td><?= $s['end_time'] ?></td>
</tr>
<?php } ?>
</table>
</div>
