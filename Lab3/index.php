<?php
$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = false;

if (isset($_POST['save_user'])) {
    $fname  = trim($_POST['first_name']);
    $lname  = trim($_POST['last_name']);
    $email  = trim($_POST['email']);
    $gender = $_POST['gender'] ?? '';
    $dept   = trim($_POST['department']);
    $skills = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : 'None';
    
    $image_name = 'default.png'; 

    if (empty($fname)) $errors[] = "First name is required.";
    if (empty($lname)) $errors[] = "Last name is required.";
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($gender)) $errors[] = "Please select your gender.";

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $file = $_FILES['profile_image'];
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; 

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }

        if ($file['size'] > $max_size) {
            $errors[] = "File size too large. Maximum size is 2MB.";
        }

        if (empty($errors)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $image_name = time() . '_' . uniqid() . '.' . $ext;
            $target_path = 'uploads/' . $image_name;

            if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                $errors[] = "Failed to upload image.";
                $image_name = 'default.png'; 
            }
        }
    }


    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, gender, department, skills, profile_image) VALUES ('$fname', '$lname', '$email', '$gender', '$dept', '$skills', '$image_name')");
        

        if ($stmt->execute()) {
            header("Location: view_users.php?status=success");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User Form with Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-container { max-width: 800px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #444; }
        .btn-add { background-color: #6366f1; border: none; padding: 10px 30px; color: white; }
        .bg-light-blue { background-color: #f0f4ff !important; border-color: #dbeafe !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="mb-4">User Registration</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" enctype="multipart/form-data">
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
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="Open Source" value="<?= htmlspecialchars($dept ?? '') ?>">
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
                <div class="col-md-6">
                    <label class="form-label">Email (Username)</label>
                    <input type="email" name="email" class="form-control bg-light-blue" placeholder="user@gmail.com" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control">
                    <small class="text-muted">Accepted: JPG, PNG. Max: 2MB.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php 
                        $skill_options = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
                        foreach ($skill_options as $skill): ?>
                        <div class="col-4 col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?= $skill ?>" <?= in_array($skill, $selected_skills) ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= $skill ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <button type="submit" name="save_user" class="btn btn-primary btn-add">Save User</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>