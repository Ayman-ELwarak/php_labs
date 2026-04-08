<?php
$file = 'customer.txt';
?>


<?php
// handle delete logic

$file = 'customer.txt';

if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];

    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $remaining_lines = [];

        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if ($parts[0] != $id_to_delete) {
                $remaining_lines[] = $line;
            }
        }
        file_put_contents($file, implode(PHP_EOL, $remaining_lines) . PHP_EOL);
    }
    header("Location: view_users.php?status=deleted");
    exit();
}
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
                if (file_exists($file)) {
                    $records = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($records as $line) {
                        $cols = explode('|', $line);
                        if (count($cols) >= 4) {
                            echo "<tr>";
                            foreach ($cols as $val) echo "<td>" . ($val) . "</td>";
                            echo "<td>
                                    <a href='view_users.php?delete_id=" . ($cols[0]) . "' 
                                       class='btn btn-danger btn-sm' 
                                       onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>