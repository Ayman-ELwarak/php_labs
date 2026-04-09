<?php
$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata"; 

$connection = new mysqli($host, $user, $pass, $db);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// handle delete logic
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];

    $stmt = $connection->prepare("DELETE FROM users WHERE id = '$id_to_delete'");
    
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
</head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div class="alert alert-warning">Record deleted successfully.</div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Registered Customers</h2>
            <a href="index.php" class="btn btn-primary">Add New +</a>
        </div>

        <table class="table table-striped border">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
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
                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . ucfirst($row['gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['skills']) . "</td>";
                        echo "<td>
                                <a href='view_users.php?delete_id=" . $row['id'] . "' 
                                   class='btn btn-danger btn-sm' 
                                   onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
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