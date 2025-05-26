<?php
require __DIR__.'/includes/config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $user = $conn->query("SELECT id FROM users WHERE email='$email'")->fetch_assoc();
    
    if($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $conn->query("
            INSERT INTO password_resets 
            SET user_id = {$user['id']},
                token = '$token',
                expires_at = '$expires'
        ");
        
        // Send email (configure your SMTP settings)
        $reset_link = "http://localhost/mlm_website/reset_password.php?token=$token";
        mail($email, "Password Reset", "Click here: $reset_link");
        
        $_SESSION['message'] = "Reset link sent to your email";
    } else {
        $error = "Email not found";
    }
}

require __DIR__.'/includes/header.php';
?>

<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>
