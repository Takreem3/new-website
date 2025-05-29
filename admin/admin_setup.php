<?php
require 'includes/config.php';

// Generate a secure random key
$secret_key = "MLM-ADMIN-KEY-" . bin2hex(random_bytes(16));

// Create admin user directly
$username = 'admin';
$email = 'admin@yourdomain.com';
$password = password_hash('your_secure_password', PASSWORD_DEFAULT);

$conn->query("
    INSERT INTO users (username, email, password, is_admin, payment_status)
    VALUES ('$username', '$email', '$password', 1, 'verified')
");

echo "Admin account created successfully!<br>";
echo "Your secret registration key (save this): <strong>$secret_key</strong><br>";
echo "Delete this file immediately after use!";
?>