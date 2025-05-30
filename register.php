<?php
session_start();
require 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple validation
    $required = ['username', 'email', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $error = "All fields are required";
            break;
        }
    }

    if (!$error) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sponsor = $conn->real_escape_string($_POST['sponsor'] ?? '');

        // Sponsor validation
        $sponsor_id = null;
        if (!empty($sponsor)) {
            $result = $conn->query("SELECT id FROM users WHERE username = '$sponsor'");
            if ($result->num_rows > 0) {
                $sponsor_id = $result->fetch_assoc()['id'];
            }
        }

        // Insert user
        $sql = "INSERT INTO users (username, email, password, sponsor_id) 
                VALUES ('$username', '$email', '$password', " . ($sponsor_id ?: 'NULL') . ")";
        
        if ($conn->query($sql)) {
            $success = "Registration successful! Please login.";
            header("refresh:2;url=login.php");
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center">Create Account</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sponsor (Optional)</label>
                            <input type="text" name="sponsor" class="form-control">
                            <div class="form-text">Enter sponsor's username if you were referred</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                        
                        <div class="mt-3 text-center">
                            Already have an account? <a href="login.php">Login</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
