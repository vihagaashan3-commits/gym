<?php
session_start();
include("../config/db.php");
if($_SESSION['user']['role'] != 'admin'){ header("Location: ../main/login.php"); exit(); }

// Add trainer
if(isset($_POST['add_trainer'])){
    $name = $_POST['full_name'];
    $spec = $_POST['specialization'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    $fileName = null;
    if(!empty($_FILES['profile_pic']['name'])){
        $fileName = time().'_'.$_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], "../assets/uploads/".$fileName);
    }

    $stmt = $conn->prepare("INSERT INTO trainers (full_name,specialization,phone,email,profile_pic) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssss",$name,$spec,$phone,$email,$fileName);
    $stmt->execute();
}

// Fetch trainers
$trainers = $conn->query("SELECT * FROM trainers ORDER BY full_name");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/manage_trainers.css">

<div class="content">
<h1>Manage Trainers</h1>

<div class="add-trainer-form">
<form method="POST" enctype="multipart/form-data">
<input type="text" name="full_name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="phone" placeholder="Phone">
<select name="specialization">
<option value="muscle_building">Muscle Building</option>
<option value="weight_loss">Weight Loss</option>
<option value="cardio">Cardio</option>
<option value="flexibility">Flexibility</option>
</select>
<input type="file" name="profile_pic">
<button name="add_trainer" class="btn">Add Trainer</button>
</form>
</div>

<h2>All Trainers</h2>
<table>
<tr><th>Name</th><th>Specialization</th><th>Phone</th><th>Email</th></tr>
<?php while($t=$trainers->fetch_assoc()){ ?>
<tr>
<td><?= $t['full_name'] ?></td>
<td><?= str_replace('_',' ',$t['specialization']) ?></td>
<td><?= $t['phone'] ?></td>
<td><?= $t['email'] ?></td>
</tr>
<?php } ?>
</table>
</div>
