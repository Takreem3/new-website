<?php
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) die("Access denied");
require '../includes/config.php';

// Only allow this page to be accessed from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die("Admin registration can only be done locally");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $secret_key = $_POST['secret_key'];
    
    // Verify secret key (change this to your own secure key)
   $valid_key = "MLM-ADMIN-KEY-" . bin2hex(random_bytes(16)); // Example: MLM-ADMIN-KEY-1a2b3c4d5e6f7g8h
    
    if ($secret_key !== $valid_key) {
        $error = "Invalid registration key";
    } elseif (strlen($password) < 12) {
        $error = "Password must be at least 12 characters";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $conn->query("
            INSERT INTO users (username, email, password, is_admin, payment_status)
            VALUES ('$username', '$email', '$password_hash', 1, 'verified')
        ");
        
        $success = "Admin account created successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3 class="text-center">Admin Registration</h3>
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
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password (min 12 chars)</label>
                                    <input type="password" name="password" class="form-control" required minlength="12">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secret Registration Key</label>
                                    <input type="password" name="secret_key" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-danger w-100">Create Admin Account</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>