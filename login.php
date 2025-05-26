<?php
require __DIR__.'/includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $user = $conn->query("SELECT * FROM users WHERE email = '$email'")->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}

require __DIR__.'/includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> User Login</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>

                        <!-- Forgot Password Link (Added Here) -->
                        <div class="text-center mt-3">
                            <a href="request_reset.php" class="text-decoration-none">
                                <i class="bi bi-question-circle"></i> Forgot Password?
                            </a>
                        </div>
                    </form>

                    <!-- Registration Prompt -->
                    <div class="text-center mt-3">
                        <p>New user? <a href="register.php">Create an account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>
