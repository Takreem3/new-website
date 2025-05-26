<?php
require __DIR__.'/includes/config.php';
require __DIR__.'/includes/auth.php';
authOnly();

// Fetch user network data
$network = $conn->query("SELECT * FROM users WHERE sponsor_id = {$_SESSION['user_id']}");

require __DIR__.'/includes/header.php';
?>
<div class="container">
    <h2>Your Network Tree</h2>
    <!-- Display tree here -->
</div>
<?php require __DIR__.'/includes/footer.php'; ?>