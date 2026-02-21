<?php
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only customer
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer'){
    header("Location: ../main/login.php");
    exit();
}

$userId = $_SESSION['user']['id'];

// Determine the month/year (default current)
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$firstDayOfMonth = "$year-" . sprintf("%02d", $month) . "-01";
$daysInMonth = date('t', strtotime($firstDayOfMonth));

// Fetch attendance for this month
$stmt = $conn->prepare("
    SELECT date, status 
    FROM attendance 
    WHERE user_id=? AND MONTH(date)=? AND YEAR(date)=?
");
$stmt->bind_param("iii", $userId, $month, $year);
$stmt->execute();
$attendanceData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Map attendance by date
$attendanceMap = [];
$present = 0;
$absent = 0;

foreach($attendanceData as $a){
    $date = $a['date'];
    $status = $a['status'];
    $attendanceMap[$date] = $status;

    if($status == 'present') $present++;
    else $absent++;
}

$totalDays = $present + $absent;
$percentPresent = $totalDays>0 ? round(($present/$totalDays)*100) : 0;
$percentAbsent  = $totalDays>0 ? round(($absent/$totalDays)*100) : 0;
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/customer_attendance.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content">
    <h1 style="color:white;">My Attendance</h1>

    <!-- Month Navigation -->
    <div class="month-nav">
        <?php 
        $prevMonth = $month-1 < 1 ? 12 : $month-1;
        $prevYear  = $month-1 < 1 ? $year-1 : $year;
        $nextMonth = $month+1 > 12 ? 1 : $month+1;
        $nextYear  = $month+1 > 12 ? $year+1 : $year;
        ?>
        <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="nav-btn">&lt; Prev</a>
        <span class="current-month" style="color:white"><?= date('F Y', strtotime("$year-$month-01")) ?></span>
        <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="nav-btn">Next &gt;</a>
    </div>

    <!-- Stats -->
    <div class="attendance-stats">
        <div class="stat-card present-card">
            <h3>Present</h3>
            <p><?= $percentPresent ?>%</p>
        </div>
        <div class="stat-card absent-card">
            <h3>Absent</h3>
            <p><?= $percentAbsent ?>%</p>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <h3 style="color:white">Attendance Calendar</h3>
        <div class="calendar-grid">
            <?php
            // Determine starting day of week
            $firstWeekday = date('N', strtotime($firstDayOfMonth)); // 1=Mon
            for($i=1; $i<$firstWeekday; $i++) echo "<div class='calendar-day empty'></div>";

            for($d=1; $d<=$daysInMonth; $d++){
                $dayStr = sprintf("%02d", $d);
                $dateKey = "$year-" . sprintf("%02d",$month) . "-$dayStr";
                $statusClass = $attendanceMap[$dateKey] ?? 'no-data';
                $todayClass = ($dateKey == date('Y-m-d')) ? 'today' : '';
                echo "<div class='calendar-day $statusClass $todayClass' data-date='$dateKey' title='Click for details'>$d</div>";
            }
            ?>
        </div>

        <div class="calendar-legend">
            <span class="legend present">Present</span>
            <span class="legend absent">Absent</span>
            <span class="legend no-data">No Data</span>
            <span class="legend today">Today</span>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-section">
        <canvas id="attendanceChart"></canvas>
    </div>

</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modal-date"></h3>
        <p id="modal-status"></p>
    </div>
</div>

<script>
// Doughnut Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Present','Absent'],
        datasets:[{
            data: [<?= $present ?>, <?= $absent ?>],
            backgroundColor:['#10b981','#ef4444']
        }]
    },
    options:{ responsive:true, plugins:{ legend:{ position:'bottom' } } }
});

// Modal functionality
const modal = document.getElementById('modal');
const modalDate = document.getElementById('modal-date');
const modalStatus = document.getElementById('modal-status');
const closeBtn = document.querySelector('.close');

document.querySelectorAll('.calendar-day').forEach(day=>{
    day.addEventListener('click',()=>{
        const date = day.dataset.date;
        const status = day.classList.contains('present') ? 'Present' :
                       day.classList.contains('absent') ? 'Absent' : 'No Data';
        modalDate.innerText = date;
        modalStatus.innerText = "Status: " + status;
        modal.style.display = 'block';
    });
});

closeBtn.onclick = () => { modal.style.display='none'; };
window.onclick = (e) => { if(e.target == modal) modal.style.display='none'; };
</script>
