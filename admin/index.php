<?php
require 'includes/auth_check.php';
require dirname(__DIR__, 2) . '/includes/header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>
    <ul>
        <li><a href="users.php">Manage Users</a></li>
        <li><a href="kyc.php">Review KYC Submissions</a></li>
    </ul>
</div>

<?php require dirname(__DIR__, 2) . '/includes/footer.php'; ?>