<?php
// ==================== SESSION & DB ====================
if(session_status() == PHP_SESSION_NONE) session_start();
include("../config/db.php");

// Only admin access
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

// ==================== ADD PLAN ====================
if(isset($_POST['add_plan'])){
    $name = $_POST['plan_name'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO plans (plan_name,duration,price) VALUES (?,?,?)");
    $stmt->bind_param("sid", $name, $duration, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: plans.php"); // reload page
    exit();
}

// ==================== UPDATE PLAN ====================
if(isset($_POST['update_plan'])){
    $id = $_POST['plan_id'];
    $name = $_POST['plan_name'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE plans SET plan_name=?, duration=?, price=? WHERE id=?");
    $stmt->bind_param("sidi", $name, $duration, $price, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: plans.php");
    exit();
}

// ==================== DELETE PLAN ====================
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM plans WHERE id=$id");
    header("Location: plans.php");
    exit();
}

// ==================== FETCH ALL PLANS ====================
$plans = $conn->query("SELECT * FROM plans");
?>

<?php include("../config/sidebar.php"); ?>

<div class="content">
    <h1>Manage Plans</h1>

    <!-- Add Plan Form -->
    <div class="form-card">
        <h3>Add New Plan</h3>
        <form method="POST">
            <input type="text" name="plan_name" placeholder="Plan Name" required>
            <input type="number" name="duration" placeholder="Duration (Months)" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <button type="submit" name="add_plan" class="btn primary-btn">Add Plan</button>
        </form>
    </div>

    <!-- Plans Table -->
    <div class="table-card">
        <h3>Existing Plans</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $plans->fetch_assoc()){ ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['plan_name'] ?></td>
                    <td><?= $row['duration'] ?> Months</td>
                    <td>$<?= $row['price'] ?></td>
                    <td>
                        <!-- Edit Modal Trigger -->
                        <button class="btn edit-btn" onclick="openEditModal(<?= $row['id'] ?>,'<?= $row['plan_name'] ?>',<?= $row['duration'] ?>,<?= $row['price'] ?>)">Edit</button>
                        <a href="?delete=<?= $row['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure to delete this plan?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal()">&times;</span>
        <h3>Edit Plan</h3>
        <form method="POST">
            <input type="hidden" name="plan_id" id="edit_id">
            <input type="text" name="plan_name" id="edit_name" required>
            <input type="number" name="duration" id="edit_duration" required>
            <input type="number" step="0.01" name="price" id="edit_price" required>
            <button type="submit" name="update_plan" class="btn primary-btn">Update Plan</button>
        </form>
    </div>
</div>

<!-- JS for Edit Modal -->
<script>
function openEditModal(id,name,duration,price){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_duration').value = duration;
    document.getElementById('edit_price').value = price;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal(){
    document.getElementById('editModal').style.display = 'none';
}
</script>

<!-- CSS for professional UI -->
<link rel="stylesheet" href="../assets/plans.css">
