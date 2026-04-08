<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .form-label { font-weight: 600; color: #444; }
        .btn-add { background-color: #6366f1; border: none; padding: 10px 30px; }
        .btn-add:hover { background-color: #4f46e5; }
        .captcha-box {
            background: #fdfdfd;
            border: 1px solid #eee;
            padding: 10px 20px;
            font-family: 'Courier New', Courier, monospace;
            font-style: italic;
            letter-spacing: 5px;
            font-weight: bold;
        }
        .bg-light-blue { background-color: #f0f4ff !important; border-color: #dbeafe !important; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <form action="user_details.php" method="POST">
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
                        <option selected>Select a country</option>
                        <option value="Egypt">Egypt</option>
                        <option value="USA">USA</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Enter full street address, city, and postal code..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                    <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                    <label class="form-check-label" for="female">Female</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                    <label class="form-check-label" for="other">Other</label>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control bg-light-blue" placeholder="user1@gmail.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control bg-light-blue"       >
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Professional Skills</label>
                <div class="border rounded p-3 bg-light">
                    <div class="row">
                        <?php 
                        $skills = ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Python'];
                        foreach ($skills as $skill): ?>
                        <div class="col-6 col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?php echo $skill; ?>">
                                <label class="form-check-label"><?php echo $skill; ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Verification</label>
                <div class="d-flex align-items-center gap-3 p-2 border rounded">
                    <div class="captcha-box">X 7 2 B P</div>
                    <input type="text" name="captcha" class="form-control" style="max-width: 200px;" placeholder="Enter captcha">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <button type="button" class="btn btn-link text-secondary text-decoration-none">Cancel</button>
                <button type="submit" class="btn btn-primary btn-add">Add User</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>