<?php
require __DIR__.'/includes/config.php';

$token = $_GET['token'] ?? '';
$valid = false;

if($token) {
    $reset = $conn->query("
        SELECT * FROM password_resets 
        WHERE token = '$token' 
        AND used = 0 
        AND expires_at > NOW()
    ")->fetch_assoc();
    
    $valid = (bool)$reset;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && $valid) {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $conn->query("UPDATE users SET password='$password' WHERE id={$reset['user_id']}");
    $conn->query("UPDATE password_resets SET used=1 WHERE id={$reset['id']}");
    
    $_SESSION['message'] = "Password updated! Please login";
    header("Location: login.php");
    exit();
}

require __DIR__.'/includes/header.php';
?>

<div class="container">
    <?php if($valid): ?>
        <h2>Reset Password</h2>
        <form method="POST">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Invalid or expired reset link</div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>