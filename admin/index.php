<?php
require '../database.php';
require 'includes/auth_check.php';

echo '<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <nav>
        <a href="users.php">Manage Users</a>
        <a href="kyc.php">KYC Approvals</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <div class="content">
        <h2>Welcome, Admin!</h2>
        <!-- Admin content here -->
    </div>
</body>
</html>';