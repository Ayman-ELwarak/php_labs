<?php
require_once __DIR__ . '/autoload.php';

Auth::start();
Auth::requireLogin();

$errors = [];
$userRepository = new UserRepository(Database::getConnection());
$isEdit = false;
$userId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$user = new User();

if ($userId) {
    $existingUser = $userRepository->findById($userId);

    if (!$existingUser) {
        die('User not found.');
    }

    $user = $existingUser;
    $isEdit = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $user->firstName = trim($_POST['first_name'] ?? '');
    $user->lastName = trim($_POST['last_name'] ?? '');
    $user->email = trim($_POST['email'] ?? '');
    $user->gender = $_POST['gender'] ?? '';
    $user->department = trim($_POST['department'] ?? '');
    $user->setSkills($_POST['skills'] ?? []);

    $password = $_POST['password'] ?? '';

    if (!$isEdit || $password !== '') {
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } else {
            $user->setPassword($password);
        }
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        try {
            $user->profileImage = $userRepository->saveProfileImage($_FILES['profile_image'], $user->profileImage);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($user->firstName)) {
        $errors[] = 'First name is required.';
    }
    if (empty($user->lastName)) {
        $errors[] = 'Last name is required.';
    }
    if (empty($user->email)) {
        $errors[] = 'Email is required.';
    }

    if (empty($errors)) {
        $saved = $userRepository->save($user, $isEdit && $password === '');

        if ($saved) {
            header('Location: view_users.php?status=' . ($isEdit ? 'updated' : 'success'));
            exit();
        }

        $errors[] = 'Database error saving user. Please try again.';
    }
}

$selectedSkills = $user->skills !== 'None' ? explode(', ', $user->skills) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 800px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #444; }
        .btn-add { background-color: #6366f1; border: none; padding: 10px 30px; color: white; }
        .btn-add:hover { background-color: #4f46e5; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><?= $isEdit ? 'Update User' : 'User Registration' ?></h2>
            <div>
                <span class="me-3 text-muted">Hi, <?= Auth::getUserName() ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php<?= $isEdit ? '?id=' . $user->id : '' ?>" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user->firstName) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user->lastName) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <?= $isEdit ? '<small class="text-muted">(Leave blank to keep current)</small>' : '' ?></label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" <?= $isEdit ? '' : 'required' ?>>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($user->department) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="male" <?= $user->gender === 'male' ? 'checked' : '' ?> required>
                            <label class="form-check-label">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="female" <?= $user->gender === 'female' ? 'checked' : '' ?>>
                            <label class="form-check-label">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Profile Image</label>
                    <?php if ($isEdit): ?>
                        <div class="mb-2"><img src="<?= htmlspecialchars(Config::UPLOAD_URL . $user->profileImage) ?>" width="50" class="rounded border"></div>
                    <?php endif; ?>
                    <input type="file" name="profile_image" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php
                        $skillOptions = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        foreach ($skillOptions as $skill):
                        ?>
                            <div class="col-4 col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>" <?= in_array($skill, $selectedSkills, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= htmlspecialchars($skill) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between gap-3 mt-4">
                <a href="view_users.php" class="btn btn-outline-secondary">
                   &larr; View User List
                </a>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-light border">Reset Form</a>
                    <button type="submit" name="save_user" class="btn btn-primary btn-add">
                        <?= $isEdit ? 'Update User' : 'Save User' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
