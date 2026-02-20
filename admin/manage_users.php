<?php
session_start();
include("../config/db.php");

// Only admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../main/login.php");
    exit();
}

$result = $conn->query("SELECT * FROM users");
?>

<?php include("../config/sidebar.php"); ?>
<link rel="stylesheet" href="../assets/style.css">

<div class="content">
    <h2>Manage Users</h2>

    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Profile Pic</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo ucfirst($row['role']); ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td>
                    <?php if($row['profile_pic']) { ?>
                        <img src="../assets/uploads/<?php echo $row['profile_pic']; ?>" class="profile-pic">
                    <?php } else { ?>
                        <span class="no-pic">No Image</span>
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn edit-btn">Edit</a>
                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn delete-btn"
                       onclick="return confirm('Are you sure you want to delete this user?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Add this CSS at the bottom of your style.css or inside <style> for testing -->
<style>
.content {
    padding: 30px;
}

h2 {
    margin-bottom: 20px;
    color: #333;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.user-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

.user-table thead {
    background-color: #007bff;
    color: #fff;
    font-weight: bold;
}

.user-table th, .user-table td {
    padding: 12px 15px;
    text-align: center;
}

.user-table tbody tr {
    border-bottom: 1px solid #ddd;
}

.user-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.user-table tbody tr:hover {
    background-color: #f1f1f1;
}

.profile-pic {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #007bff;
}

.no-pic {
    color: #888;
    font-size: 0.9em;
}

.btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    margin: 2px;
    display: inline-block;
}

.edit-btn {
    background-color: #28a745;
    color: #fff;
}

.delete-btn {
    background-color: #dc3545;
    color: #fff;
}

.btn:hover {
    opacity: 0.9;
}

@media screen and (max-width: 768px) {
    .user-table thead {
        display: none;
    }
    .user-table, .user-table tbody, .user-table tr, .user-table td {
        display: block;
        width: 100%;
    }
    .user-table tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
    }
    .user-table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    .user-table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: calc(50% - 30px);
        font-weight: bold;
        text-align: left;
    }
}
</style>
