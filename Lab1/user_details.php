<?php
$user = $_POST;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { 
            background-color: #f8f9fc; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #444;
        }
        .profile-card {
            max-width: 550px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .user-name {
            font-size: 1.8rem;
            color: #2d3436;
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: #b2bec3;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box {
            background-color: #f6f8ff; 
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #f0f3ff;
        }
        .info-label { 
            font-size: 0.8rem; 
            color: #a0a0a0; 
            margin-bottom: 2px; 
        }
        .info-value { 
            font-weight: 700; 
            color: #2d3436; 
            font-size: 0.95rem;
        }
        .skill-badge {
            background-color: #e9ecef;
            color: #636e72;
            padding: 8px 22px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 10px;
        }
        .credential-item {
            border: 1px solid #f1f1f1;
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .credential-icon {
            width: 42px;
            height: 42px;
            background: #f0f2ff;
            color: #6366f1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .copy-icon, .view-icon {
            color: #6366f1;
            cursor: pointer;
            font-size: 1.1rem;
            opacity: 0.6;
        }
        .copy-icon:hover { opacity: 1; }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card">
        
        <h2 class="text-center fw-bold user-name">
            <?php echo $user['first_name'] . " " . $user['last_name']; ?>
        </h2>

        <div class="section-title">
            <i class="bi bi-person"></i> PERSONAL INFORMATION
        </div>
        <div class="info-box">
            <div class="row g-4">
                <div class="col-6">
                    <div class="info-label">Department</div>
                    <div class="info-value"><?php echo ($user['department'] ?: 'Open Source'); ?></div>
                </div>
                <div class="col-6">
                    <div class="info-label">Country</div>
                    <div class="info-value"><?php echo ($user['country'] ?: 'N/A'); ?></div>
                </div>
                <div class="col-6">
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?php echo (($user['gender'] ?? 'Male')); ?></div>
                </div>
                <div class="col-6">
                    <div class="info-label">Location</div>
                    <div class="info-value"><?php echo ($user['address'] ?: 'Creative City'); ?></div>
                </div>
            </div>
        </div>

        <div class="section-title">
            <i class="bi bi-code-slash"></i> SKILLS
        </div>
        <div class="mb-4">
            <?php 
            foreach ($user['skills'] as $skill) {
                echo "<span class='skill-badge'>" . $skill . "</span>";
            }
            ?>
        </div>

        <div class="section-title">
            <i class="bi bi-lock"></i> ACCOUNT CREDENTIALS
        </div>
        <div class="credentials-group">
            <div class="credential-item">
                <div class="d-flex align-items-center">
                    <div class="credential-icon"><i class="bi bi-at"></i></div>
                    <div>
                        <div class="info-label mb-0" style="font-size: 0.7rem;">Username</div>
                        <div class="info-value"><?php echo $user['username'] ?: 'user_workspace'; ?></div>
                    </div>
                </div>
                <i class="bi bi-copy copy-icon"></i>
            </div>

            <div class="credential-item">
                <div class="d-flex align-items-center">
                    <div class="credential-icon"><i class="bi bi-three-dots"></i></div>
                    <div>
                        <div class="info-label mb-0" style="font-size: 0.7rem;">Password</div>
                        <div class="info-value">••••••••</div> 
                    </div>
                </div>
                <i class="bi bi-eye view-icon"></i>
            </div>
        </div>

    </div>
</div>

</body>
</html>