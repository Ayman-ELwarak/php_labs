<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}
?>

<?php if (isset($_SESSION['login_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>❤️ <?= $_SESSION['login_success']; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['login_success']);?>
<?php endif; ?>

<?php
$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata"; 

$connection = new mysqli($host, $user, $pass, $db);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);
    
    if ($stmt->execute()) {
        header("Location: view_users.php?status=deleted");
        exit();
    } else {
        echo "Error deleting record: " . $connection->error;
    }
    $stmt->close();
}

$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .action-btns { display: flex; gap: 5px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm">
        
        <div class="top-bar">
            <span class="text-muted">Logged in as: <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Log out from the system?')">Logout</a>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'deleted'): ?>
                <div class="alert alert-warning">Record deleted successfully.</div>
            <?php elseif ($_GET['status'] == 'success'): ?>
                <div class="alert alert-success">User added successfully.</div>
            <?php elseif ($_GET['status'] == 'updated'): ?>
                <div class="alert alert-info">User updated successfully.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Registered Customers</h2>
            <a href="index.php" class="btn btn-primary">Add New +</a>
        </div>

        <table class="table table-striped border">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Dept</th>
                    <th>Skills</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        
                        $img = !empty($row['profile_image']) ? $row['profile_image'] : 'default.png';
                        echo "<td><img src='uploads/" . $img . "' width='40' height='40' class='rounded-circle' style='object-fit: cover;'></td>";
                        
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . ucfirst($row['gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['skills']) . "</td>";
                        
                        echo "<td>
                                <div class='action-btns'>
                                    <a href='index.php?id=" . $row['id'] . "' class='btn btn-success btn-sm'>Edit</a>
                                    
                                    <a href='view_users.php?delete_id=" . $row['id'] . "' 
                                       class='btn btn-danger btn-sm' 
                                       onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$connection->close();
?>