<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
authOnly();

// Debugging (remove after fix)
error_log("Dashboard accessed by user_id: ".$_SESSION['user_id']);

$user = $conn->query("
    SELECT u.*, 
          (SELECT COUNT(*) FROM users WHERE sponsor_id = u.id) as downline_count
    FROM users u
    WHERE u.id = {$_SESSION['user_id']}
")->fetch_assoc();

require __DIR__.'/includes/header.php';
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($user['username'] ?? 'User') ?></h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Your Downline</h5>
                    <p class="display-4"><?= $user['downline_count'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__.'/includes/footer.php'; ?>