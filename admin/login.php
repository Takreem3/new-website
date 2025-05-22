<?php
session_start();
include '../includes/config.php';

// Redirect if already logged in
if(isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // Debug: Uncomment to see what's being submitted
    // file_put_contents('debug.txt', "Username: $username, Password: $password\n", FILE_APPEND);
    
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Debug: Uncomment to verify password hash
        // file_put_contents('debug.txt', "Stored Hash: ".$admin['password']."\n", FILE_APPEND);
        
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Admin account not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin: 100px auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Admin Portal</h4>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>