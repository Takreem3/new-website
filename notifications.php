<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
authOnly();

// Fetch notifications
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = {$_SESSION['user_id']}");

require __DIR__.'/includes/header.php';
?>
<div class="container">
    <h2>Your Notifications</h2>
    <!-- Display notifications here -->
</div>
<?php require __DIR__.'/includes/footer.php'; ?>