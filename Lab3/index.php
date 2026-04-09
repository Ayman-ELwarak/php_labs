<?php
// 1. Database Connection Configuration
$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if (isset($_POST['save_user'])) {
    $fname  = trim($_POST['first_name']);
    $lname  = trim($_POST['last_name']);
    $email  = trim($_POST['email']);
    $gender = $_POST['gender'] ?? '';
    $dept   = trim($_POST['department']);
    $skills = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : 'None';


    if (empty($fname)) $errors[] = "First name is required.";
    if (empty($lname)) $errors[] = "Last name is required.";
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($gender)) $errors[] = "Please select your gender.";


    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, gender, department, skills) VALUES ('$fname', '$lname', '$email', '$gender', '$dept', '$skills')");

        if ($stmt->execute()) {
            header("Location: view_users.php?status=success");
            exit();
        } else {
            $errors[] = "Execution error: " . $stmt->error;
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
    <title>Add User Form</title>
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

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success">User added successfully to the database!</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST">
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
                            <input class="form-check-input" type="radio" name="gender" value="male" <?= (isset($gender) && $gender == 'male') ? 'checked' : '' ?>>
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
                    <label class="form-label">Email (Username)</label>
                    <input type="email" name="email" class="form-control bg-light-blue" placeholder="user@gmail.com" value="<?= htmlspecialchars($email ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php 
                        $skill_options = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        foreach ($skill_options as $skill): ?>
                        <div class="col-4 col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?= $skill ?>">
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