

<?php
session_start();
$host = "localhost";
$user = "springstudent";
$pass = "springstudent";
$db   = "usersdata";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'];

                $_SESSION['login_success'] = "Welcome back, " . htmlspecialchars($user['first_name']) . "!";
                
                header("Location: view_users.php");
                exit();
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "No account found with that email.";
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
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 400px; margin: 100px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-login { background-color: #6366f1; border: none; color: white; width: 100%; padding: 10px; }
        .btn-login:hover { background-color: #4f46e5; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card">
        <h3 class="text-center mb-4">Account Login</h3>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn btn-login mb-3">Login</button>
            
            <div class="text-center">
                <p class="small mb-0">Don't have an account? <a href="index.php">Register here</a></p>
            </div>
        </form>
    </div>
</div>

</body>
</html>