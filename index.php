<?php
require 'database.php';
require 'includes/header.php';

// Simple working homepage
echo '<main class="container">';
echo '<h1>Welcome to MLM System</h1>';

if (isset($_SESSION['user_id'])) {
    $user = $conn->query("SELECT username FROM users WHERE id = {$_SESSION['user_id']}")->fetch_assoc();
    echo "<p>Welcome back, " . htmlspecialchars($user['username']) . "!</p>";
    echo '<a href="dashboard.php" class="btn">Go to Dashboard</a>';
} else {
    echo '<a href="login.php" class="btn">Login</a>';
    echo '<a href="register.php" class="btn">Register</a>';
}

echo '</main>';

require 'includes/footer.php';
?>