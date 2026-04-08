<?php
$file = 'customer.txt';

if (isset($_POST['save_user'])) {
    $fname   = trim($_POST['first_name']);
    $lname   = trim($_POST['last_name']);
    $email   = trim($_POST['email']);
    $gender  = $_POST['gender'];
    $dept    = $_POST['department'];
    $skills  = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : 'None';  // Note: This function wasn't covered in class, but I researched and implemented it to handle the skills array efficiently.

    $new_id = 1; 

    if (file_exists($file) && filesize($file) > 0) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!empty($lines)) {
            $last_line = end($lines); 
            $parts = explode('|', $last_line); 
            $last_id = (int)$parts[0]; 
            $new_id = $last_id + 1;
        }
    }

    $data = "$new_id|$fname|$lname|$email|$gender|$dept|$skills" . PHP_EOL;

    file_put_contents($file, $data, FILE_APPEND);
    
    header("Location: view_users.php?status=success");
    exit();
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

        <form action="index.php" method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="Open Source">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select name="country" class="form-select text-muted">
                        <option value="" selected>Select a country</option>
                        <option value="Egypt">Egypt</option>
                        <option value="USA">USA</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="male" required>
                    <label class="form-check-label">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="female">
                    <label class="form-check-label">Female</label>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Email (Username)</label>
                    <input type="email" name="email" class="form-control bg-light-blue" placeholder="user@gmail.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php 
                        $skills = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        foreach ($skills as $skill): ?>
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
                <button type="submit" name="save_user" class="btn btn-primary btn-add">Add User</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>