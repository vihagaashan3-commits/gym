<?php
session_start();
include("../config/db.php");

// Only admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

// Handle Add Plan
if(isset($_POST['add_workout'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO workout_plans (name, category, description) VALUES (?,?,?)");
    $stmt->bind_param("sss",$name,$category,$desc);
    $stmt->execute();
}

if(isset($_POST['add_diet'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $desc = $_POST['description'];
    $stmt = $conn->prepare("INSERT INTO diet_plans (name, category, description) VALUES (?,?,?)");
    $stmt->bind_param("sss",$name,$category,$desc);
    $stmt->execute();
}

// Fetch all plans
$workouts = $conn->query("SELECT * FROM workout_plans ORDER BY category,name");
$diets = $conn->query("SELECT * FROM diet_plans ORDER BY category,name");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/manage_plans.css">

<div class="content">
    <h1>💪 Manage Workout & Diet Plans</h1>

    <div class="plans-section">
        <h2>Add Workout Plan</h2>
        <form method="POST" class="plan-form">
            <input type="text" name="name" placeholder="Workout Name" required>
            <select name="category">
                <option value="underweight">Underweight</option>
                <option value="normal">Normal</option>
                <option value="overweight">Overweight</option>
            </select>
            <textarea name="description" placeholder="Description"></textarea>
            <button name="add_workout" class="btn">Add Workout</button>
        </form>

        <h2>Add Diet Plan</h2>
        <form method="POST" class="plan-form">
            <input type="text" name="name" placeholder="Diet Name" required>
            <select name="category">
                <option value="underweight">Underweight</option>
                <option value="normal">Normal</option>
                <option value="overweight">Overweight</option>
            </select>
            <textarea name="description" placeholder="Description"></textarea>
            <button name="add_diet" class="btn">Add Diet</button>
        </form>
    </div>

    <div class="plans-list">
        <h2>All Workout Plans</h2>
        <table>
            <tr><th>Name</th><th>Category</th><th>Description</th></tr>
            <?php while($w = $workouts->fetch_assoc()){ ?>
                <tr>
                    <td><?= $w['name'] ?></td>
                    <td><?= ucfirst($w['category']) ?></td>
                    <td><?= $w['description'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <h2>All Diet Plans</h2>
        <table>
            <tr><th>Name</th><th>Category</th><th>Description</th></tr>
            <?php while($d = $diets->fetch_assoc()){ ?>
                <tr>
                    <td><?= $d['name'] ?></td>
                    <td><?= ucfirst($d['category']) ?></td>
                    <td><?= $d['description'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
