<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}

$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$is_edit = false;
$user_id = $_GET['id'] ?? null;

if ($user_id) {
    $is_edit = true;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_user = $result->fetch_assoc();
    
    if (!$existing_user) {
        die("User not found.");
    }

    $fname  = $existing_user['first_name'];
    $lname  = $existing_user['last_name'];
    $email  = $existing_user['email'];
    $gender = $existing_user['gender'];
    $dept   = $existing_user['department'];
    $selected_skills = explode(', ', $existing_user['skills']);
    $image_name = $existing_user['profile_image'];
}

if (isset($_POST['save_user'])) {
    $fname    = trim($_POST['first_name']);
    $lname    = trim($_POST['last_name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password']; 
    $gender   = $_POST['gender'] ?? '';
    $dept     = trim($_POST['department']);
    $skills   = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : 'None';
    
    if (!$is_edit) $image_name = 'default.png'; 

    if (empty($fname)) $errors[] = "First name is required.";
    if (empty($lname)) $errors[] = "Last name is required.";
    if (!$is_edit && empty($password)) $errors[] = "Password is required.";
    if (empty($email)) $errors[] = "Email is required.";

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $file = $_FILES['profile_image'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . uniqid() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], 'uploads/' . $new_image_name)) {
            $image_name = $new_image_name;
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (empty($errors)) {
        if ($is_edit) {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password=?, gender=?, department=?, skills=?, profile_image=? WHERE id=?");
                $stmt->bind_param("ssssssssi", $fname, $lname, $email, $hashed_password, $gender, $dept, $skills, $image_name, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, gender=?, department=?, skills=?, profile_image=? WHERE id=?");
                $stmt->bind_param("sssssssi", $fname, $lname, $email, $gender, $dept, $skills, $image_name, $user_id);
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, gender, department, skills, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fname, $lname, $email, $hashed_password, $gender, $dept, $skills, $image_name);
        }

        if ($stmt->execute()) {
            header("Location: view_users.php?status=" . ($is_edit ? "updated" : "success"));
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? "Edit" : "Add" ?> User</title>
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
            <h2 class="mb-0"><?= $is_edit ? "Update User" : "User Registration" ?></h2>
            <div>
                <span class="me-3 text-muted">Hi, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php<?= $is_edit ? '?id='.$user_id : '' ?>" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($fname ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($lname ?? '') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <?= $is_edit ? '<small class="text-muted">(Leave blank to keep current)</small>' : '' ?></label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" <?= $is_edit ? '' : 'required' ?>>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($dept ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="male" <?= (isset($gender) && $gender == 'male') ? 'checked' : '' ?> required>
                            <label class="form-check-label">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" value="female" <?= (isset($gender) && $gender == 'female') ? 'checked' : '' ?>>
                            <label class="form-check-label">Female</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Profile Image</label>
                    <?php if ($is_edit): ?>
                        <div class="mb-2"><img src="uploads/<?= $image_name ?>" width="50" class="rounded border"></div>
                    <?php endif; ?>
                    <input type="file" name="profile_image" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php 
                        $skill_options = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        $current_skills = $selected_skills ?? [];
                        foreach ($skill_options as $skill): ?>
                        <div class="col-4 col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?= $skill ?>" <?= in_array($skill, $current_skills) ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= $skill ?></label>
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
                        <?= $is_edit ? "Update User" : "Save User" ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>