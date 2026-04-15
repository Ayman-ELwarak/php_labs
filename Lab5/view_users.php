<?php
require_once __DIR__ . '/autoload.php';

Auth::start();
Auth::requireLogin();

$userRepository = new UserRepository(Database::getConnection());
$flash = Auth::getFlash();

if (isset($_GET['delete_id'])) {
    $deleteId = (int) $_GET['delete_id'];

    if ($userRepository->delete($deleteId)) {
        header('Location: view_users.php?status=deleted');
        exit();
    }
}

$users = $userRepository->getAll();
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
            <span class="text-muted">Logged in as: <strong><?= Auth::getUserName() ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Log out from the system?')">Logout</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?= htmlspecialchars($flash) ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-warning">Record deleted successfully.</div>
            <?php elseif ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success">User added successfully.</div>
            <?php elseif ($_GET['status'] === 'updated'): ?>
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
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td>
                                <img src="<?= htmlspecialchars(Config::UPLOAD_URL . $user->profileImage) ?>" width="40" height="40" class="rounded-circle" style="object-fit: cover;" alt="Profile">
                            </td>
                            <td><?= htmlspecialchars($user->firstName) ?></td>
                            <td><?= htmlspecialchars($user->lastName) ?></td>
                            <td><?= htmlspecialchars($user->email) ?></td>
                            <td><?= htmlspecialchars(ucfirst($user->gender)) ?></td>
                            <td><?= htmlspecialchars($user->department) ?></td>
                            <td><?= htmlspecialchars($user->skills) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="index.php?id=<?= $user->id ?>" class="btn btn-success btn-sm">Edit</a>
                                    <a href="view_users.php?delete_id=<?= $user->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
